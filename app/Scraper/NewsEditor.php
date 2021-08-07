<?php

namespace App\Scraper;

use App\Models\News;
use Carbon\Carbon;
use Goutte\Client;
use PHPOnCouch\CouchClient;
use Symfony\Component\Console\Output\ConsoleOutput;

class NewsEditor extends CrawlerFunction{

    private $client;
    private $couch;
    private $output;

    public function __construct()
    {
        $this->client = new Client();
        $this->output = new ConsoleOutput();
        $this->couch  = new CouchClient(config('couch.host'), config('couch.db.easy'));
    }

    public function get_news_today(){
        $total = 0;
        $message = '';
        $newsID = '';

        $today = Carbon::now()->format('d/m/Y');
        $news = News::whereStatus(News::PUBLISH)->where('pub_date', $today)->get();
        foreach($news as $item){
            $text = $this->get_kanji_only($item->title . '。' . $item->content);
            if(!$this->exists_news($item->link) && !$this->is_corona($text)){
                $level = $this->get_level($text);
                $audio = $this->get_audio($text);

                $doc = $this->create_doc($item, $level, $audio);
                $new = $this->couch->storeDoc((object)$doc);
                if(isset($new->ok)){
                    $total += 1;

                    $item->success();
                    $item->save();

                    $message = $this->get_kanji_only($item->title);
                    $newsID = $new->id;
                }
            }
        }
        $this->output->writeln("Import $total news editor");

        // push notification
        $hour = Carbon::now('Asia/Ho_Chi_Minh')->format('H');
        if($total && $hour < 12){
            push_fcm($message, $total, $newsID);
            $this->output->writeln("Push notification $total news easy");
        }
        return $total;
    }

    private function create_doc(News $news, $level, $audio){
        $doc = [
            'label' => 'editor',
            'title' => $news->title,
            'link'  => $news->link,
            'date'  => Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i'),
            'description' => $news->description,
            'tag'   => null,
            'type'  => 'easy',
            'source' => $news->name_link ,
            'kind'  => $news->kind,
            'content' => [
                'image' => config('app.name') . $news->image,
                'video' => null,
                'audio' => $audio,
                'body' => $news->content
            ]
        ];
        $doc['level_tocfl'] = isset($level['tocfl']) ? $level['tocfl'] : null;
        if(!is_null($doc['level_tocfl'])){
            foreach($doc['level_tocfl'] as $key => $item){
                $doc['tocfl'][$key] = count($item);
            }
        }else{
            $doc['tocfl'] = null;
        }
        $doc['level_hsk'] = isset($level['hsk']) ? $level['hsk'] : null;
        if(!is_null($doc['level_hsk'])){
            foreach($doc['level_hsk'] as $key => $item){
                $doc['hsk'][$key] = count($item);
            }
        }else{
            $doc['hsk'] = null;
        }
        
        return $doc;
    }

    /**
     * Remove tag ruby and rt
     * 
     * @return string
     */
    private function get_kanji_only($string){
        $regex = '/<ruby.*?>(.+?)<rt.*?<\/rt>.*?<\/ruby>/';
        return strip_tags(preg_replace($regex, '$1', $string));
    }

    private function exists_news($link){
        $doc = $this->couch->key($link)->getView('search', 'links');
        return ($doc->rows) ? true : false;
    }
}