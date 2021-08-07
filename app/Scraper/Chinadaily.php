<?php

namespace App\Scraper;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Goutte\Client;
use PHPOnCouch\CouchClient;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DomCrawler\Crawler;
use File;

class Chinadaily extends CrawlerFunction{
    const url = 'http://cn.chinadaily.com.cn/';

    private $client;
    private $couch;
    private $output;
    private $newsEditor;
    private $message;
    private $newsID;

    public function __construct()
    {
        $this->client = new Client();
        $this->output = new ConsoleOutput();
        $this->couch  = new CouchClient(config('couch.host'), config('couch.db.easy'));
        $this->newsEditor = new NewsEditor();
    }

    public function scraper(){   
        // Get news editor
        $this->newsEditor->get_news_today();

        $crawler = $this->get_content_html(self::url);
        if($crawler !== false){
            // Get news topic
            $topic = $this->get_news_topic($crawler);
            
            // Get news list
            $list = $this->get_news_list($crawler);

            $total = $topic + $list;
            $hour = Carbon::now('Asia/Ho_Chi_Minh')->format('H');
            if($total && $hour < 12){
                push_fcm($this->message, $total, $this->newsID, 'normal');
                $this->output->writeln("Push notification $total news normal");
            }
            
        }else{
            $this->output->writeln("Cannot get news");
        }
    }

    private function get_content_html($url){
        $crawler = $this->client->request('GET', $url);
        $response = $this->client->getResponse();
        if($response->getStatusCode() == 200){
            return $crawler;
        }
        return false;
    }

    private function get_news_list($crawler){
        $total = 0;
        try{
            $crawler->filter('.container>.container-left>.left-liebiao>.busBox1')->each(function(Crawler $node) use(&$total){
                $news = [
                    'title' => trim($this->rmTagFont_FigureImg($node->filter('div>div>h3>a')->text())),
                    'link'  => $node->filter('div>div>h3>a')->attr('href'),
                    'description' => trim($this->rmTagFont_FigureImg($node->filter('div>div>p')->text())),
                    'image' => ($node->filter('div>.mr10>a>img')->count()) ? $node->filter('div>.mr10>a>img')->attr('src') : null,
                    'tag'   => $this->rmTagFont_FigureImg($node->filter('div>div>.chinese>span.jk>a')->text()),
                    'label' => 'list'
                ];

                $insert = $this->store_news($news);
                ($insert) ? $total++ : null;
            });
            $this->output->writeln("Import $total news list");
        }catch(\Exception $e){
            $this->output->writeln($e->getMessage());
        }
        return $total;
    }
    
    private function get_news_topic($crawler){
        $total = 0;
        try{
            $crawler->filter('.container>.container-left>.left-datu>.jiaot-yu>.focus>.fPic>.fcon')->each(function(Crawler $node) use(&$total){
                $topic = [
                    'title' => $this->rmTagFont_FigureImg($node->filter('div>h3>a')->text()),
                    'link'  => $node->filter('a')->attr('href'),
                    'description' => null,
                    'image' => ($node->filter('a>img')->count()) ? $node->filter('a>img')->attr('src') : null,
                    'tag'   => null,
                    'label' => 'topic',
                ];

                $add = $this->store_news($topic);
                ($add) ? $total++ : null;
            });
            $this->output->writeln("Import $total news topic");
        }catch(\Exception $e){
            $this->output->writeln($e->getMessage());
        }
        return $total;
    }

    private function get_detail_news($url, $title = ''){
        $news = $this->get_content_html($url);
        if($news !== false){
            if($news->filter('.dat>#Content>#div_currpage')->count() || 
                $news->filter('.container>.container-left2>#Content>#div_currpage')->count() ||
                $news->filter('.container>.container-left>#Content>#div_currpage')->count()){
                return null;
            }
            $elementDate = '';
            $elementContent = '';
            if($news->filter('.dat')->count()){
                $elementDate = '.dat>.fenx>.xinf-le';
                $elementContent = '.dat>#Content';
            }else if($news->filter('.container>.container-left2')->count()){
                $elementDate = '.container>.container-left2>.fenx>.xinf-le';
                $elementContent = '.container>.container-left2>#Content';
            }else if($news->filter('.container>.container-left')->count()){
                $elementDate = '.container>.container-left>.fenx>.xinf-le';
                $elementContent = '.container>.container-left>#Content';
            }else{
                return null;
            }
            $date = $news->filter($elementDate)->last()->text();
            $content = $this->rmTagFont_FigureImg($news->filter($elementContent)->html());
            $content = preg_replace(['/(（)*(编辑|制片人|主编|记者|制图|新华社发|英文来源|翻译|&|出品人|策划|监制|文字|摄影|统筹|视觉|\|| )\s*(：|丨|（).*(）)/', '/<figcaption((.|\n)*?)<\/figcaption>/', '/【(責任編輯|责任编辑).*?】/', '/(\(|（)(版權所有|未經授權不得轉載|中國日報湖北記者站|編譯|完).*?(）|\))/'], '', $content);
            $text = trim(remove_newlines(strip_tags($content)));

            // Cannot get news over 2000 charactor or key words has corona
            if(mb_strlen($text) > 2000 || $this->is_corona($text)){
                return null;
            }

            // Replace and remove attributes
            $regex = ['/style=\".*?\"/', '/id=\".*?\"/', '/align=\".*?\"/', '/ \/\//', '/<p[^>]*>(?:\s|&nbsp;)*<\/p>/'];
            $content = remove_newlines(preg_replace($regex, '', strip_tags($content, '<img><p><figure>')));
            $content = $this->remove_attributes($content, $title);
            $date = trim(str_replace('　', '', $date));

            return [
                'date'  => $date,
                'body'  => $content['body'],
                'audio' => $content['audio'],
                'text'  => $text
            ];
        }
        return null;
    }

    private function store_news(array $news){
        // Check exists link
        if(!$this->exists_news($news['link'])){
            // Crawler detail news
            try{
                $detail = $this->get_detail_news($news['link'], $news['title']);

                if(!is_null($detail)){
                    $data = [
                        'label' => $news['label'],
                        'title' => $this->attach_pinyin($this->attach_level($news['title'])),
                        'link'  => $news['link'],
                        'date'  => $detail['date'],
                        'description' => $this->attach_pinyin($this->attach_level($news['description'])),
                        'tag'   => $news['tag'],
                        'type'  => 'normal',
                        'source' => 'Chinadaily',
                        'content' => [
                            'image' => $news['image'],
                            'video' => null,
                            'audio' => $detail['audio'],
                            'body' => $detail['body']
                        ]
                    ];

                    // get level tocfl and hsk
                    $text = $news['title'] . '。' . $news['description'] . '。' . $detail['text'];
                    $level = $this->get_level($text);
                    $data['level_tocfl'] = isset($level['tocfl']) ? $level['tocfl'] : null;
                    if(!is_null($data['level_tocfl'])){
                        foreach($data['level_tocfl'] as $key => $item){
                            $data['tocfl'][$key] = count($item);
                        }
                    }else{
                        $data['tocfl'] = null;
                    }
                    $data['level_hsk'] = isset($level['hsk']) ? $level['hsk'] : null;
                    if(!is_null($data['level_hsk'])){
                        foreach($data['level_hsk'] as $key => $item){
                            $data['hsk'][$key] = count($item);
                        }
                    }else{
                        $data['hsk'] = null;
                    }
                    $new = $this->couch->storeDoc((object)$data);
                    if(isset($new->ok)){
                        //save image on serve, then update file path
                        $save = $this->saveImgOnServer($news['image'], $new->id);
                        $data['content']['image'] = $save;
                        $data['_id'] = $new->id;
                        $data['_rev'] = $new->rev;

                        $update = $this->couch->storeDoc((object) $data);
                        if(isset($update->ok)){
                            // data push notification
                            $this->message = $news['title'];
                            $this->newsID = $new->id;
                            return true;
                        }
                    }
                }
            }catch(\Exception $e){
                $this->output->writeln($e->getMessage());
                return false;
            }
        }
        return false;
    }

    private function remove_attributes($string, $title = ''){
        $textAudio = strip_tags($title . '。');

        $dom = new DOMDocument();
        @$dom->loadHTML('<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>' . $string . '</body></html>');
        $xpath = new DOMXPath($dom);
        $body = $xpath->query('/html/body')->item(0);
        foreach($xpath->query('img|p', $body) as $tag){
            $toRemove = null;
            foreach ($tag->attributes as $attr) {
                if ('src' !== $attr->name) {
                    $toRemove[] = $attr;
                }
            }
            if ($toRemove) {
                foreach ($toRemove as $attr) {
                    $tag->removeAttribute($attr->name);
                }
            }
            // 
            if($tag->nodeName == 'p'){
                $textAudio .= strip_tags($tag->nodeValue);
            }
            $tag->nodeValue = $this->attach_level($tag->nodeValue);
        }
        
        // convert the document back to a HTML string
        $html = '';
        foreach ($body->childNodes as $node) {
            $str = $dom->saveHTML($node);
            $html .= $this->attach_pinyin($str);
        }

        $audio = null;
        try{
            $audio = $this->get_audio($textAudio);
        }catch(\Exception $e){
            $this->output->writeln('Cannot get audio');
        }

        return [
            'body'  => html_entity_decode(trim($html)),
            'audio' => $audio
        ];
    }

    private function exists_news($link){
        $doc = $this->couch->key($link)->getView('search', 'links');
        return ($doc->rows) ? true : false;
    }

    private function saveImgOnServer($img_url, $id){
        if(!trim($img_url)){
            return null;
        }
        $real_url = trim($img_url,'//');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL,$real_url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        $img_data = curl_exec($curl);
        curl_close($curl);

        $filename = $id.'.jpeg';
        $path = 'public/news/';
        $file_path = $path . $filename;
        $save = File::put($file_path, $img_data);

        $real_path = preg_replace('/http:|https:/','',url('news/'. $filename)); //remove http url.
        return ($save) ? $real_path : null;
    }
}