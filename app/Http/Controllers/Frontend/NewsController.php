<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\ValidateController;
use PHPOnCouch\CouchClient;
use Session;
use Response;
use DateTime;
use \stdClass;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;

class NewsController extends Controller
{
    public $validate;

    const _limit = 10;
    const SEARCH_DESIGN = 'search';
    const VIEW_BY_DATE = 'by_date';
    const VIEW_EASY_MUTIPLE = 'type_easy_mutiple';
    const VIEW_EASY_MUTIPLE_DATE = 'type_easy_mutiple_date';
    const VIEW_NORMAL_MUTIPLE = 'type_normal_mutiple';
    const VIEW_NORMAL_MUTIPLE_DATE = 'type_normal_mutiple_date';

    public function __construct(){
        $this->proverb_url = "https://dict.naver.com/linedict/en/homeDataJson.dict?dicType=cnen";
    	$this->validate = new ValidateController();
        $this->util = new UtilController();
        $this->seo = new SeoController();
        $this->news = new CouchClient(config('couch.host'), config('couch.db.easy'));
    }

    public function language(Request $request){
        $lang = $request->get('locale');
        $this->validate->changeLang($lang);

        if ($request->ajax()) {
            return Response::json('Success', 200);
        } else {
            return redirect()->back();
        }
    }

    public function show($type = 'normal', $topic = 'all', $source = 'all', Request $request) {
        $lang = $request->hl ? $request->hl : 'vi-VN';
        $this->validate->changeLang($lang);
      
        Session::put('type', $type);
        Session::put('topic', $topic);
        Session::put('source', $source);

        $proverb = $this->getProverb();

        // try{
          
            $lists = array();
            $date = Carbon::now();

            //get news for type (level), topic, source or day
            if ($topic == 'all') {
                if ($source == 'all') {
                    $days_limit = date('Y/m/d',strtotime("-5 days"));
                    $limit = 40;
    
                    do {
                        $dateGet = $date->format('Y-m-d');
                        $lists = $this->filterNews($limit, 1, $dateGet, '', '', $type); 

                        if (sizeof($lists) == 0) {
                            $date->subday(1);
                        }
                    } while ( ($lists == null) && ($date->format("Y/m/d") !== $days_limit ));
                } else {
                    $lists = $this->filterNews(20, 1, '', '', $source, $type);
                }
            } else {
                $lists = $this->filterNews(20, 1, '', $topic, $source, $type);
            }

            // get data news in yesterday
            $date->subday(1);
            $limitAgo = 30;
            $day_limit_ago = $days_limit = date('Y/m/d',strtotime("-8 days"));

            do {
                $yesterdayGet = $date->format('Y-m-d');
                $newsAgo = new stdClass();
                $dataNew= $this->filterNews($limitAgo,1, $yesterdayGet, null, null, $type ); 

                $newsAgo->data = $dataNew;
                // $newsAgo->data = [];
                $newsAgo->date = $date->format('M j');

                if ($dataNew == null) {
                    $date->subday(1);
                }
            } while ( ($dataNew == null) && ($date->format("Y/m/d") !== $day_limit_ago ));
            
            //list 5 day after today
            $listDateAgo = [];
            for( $i = 1; $i < 5; $i++) {
                $temp = $date->subday(1);
                $listDateAgo[$temp->format('M j')] = $temp->format('Y-m-d');
            }

            if($lists == null) {
                $lists = [];
            }

            $top = [];
            foreach ($lists as $k => $value) {
                if ($k == 0) {
                    $firstNews = $value;
                } else {
                    array_push($top, $value);
                }
            }

            $seoWeb = $this->seo->setSeoHome('seo_home_all',  Session::get('locale'), $request->fullUrl());

            // dd($firstNews);

            $view = view('frontend.news.index');
            $view->with('seoWeb', $seoWeb);
            $view->with('top', $top);
            $view->with('firstNews', $firstNews);
            $view->with('newsAgo', $newsAgo);
            $view->with('listDateAgo', $listDateAgo);
            $view->with('proverb', $proverb);
            return $view;
        // } catch(\Exception $e) {
        //     return redirect()->route('error');
        // }
    }

    public function showDetail(Request $request, $newsId) { 
        $lang = $request->hl ? $request->hl : 'vi-VN';
        $this->validate->changeLang($lang);

        $type = Session::get('type');
        // $count = $this->getCountTranslate($newsId);
        $detail = $this->getDetailNews($request->id);

        if ($detail == null) {
            return redirect()->route('error');
        }

        // $id = $request->id;
        // if ($type == 'normal') {
            // $detail->content->textbody = $detail->content->textbody .$detail->content->textmore;

            // remove div tag
            // $dataContent =  $detail->content->textbody;
            // $dataContent = str_replace(['<div>', '</div>'], '', $dataContent);
            // $dataContent = str_replace('<br><br>', '<br>', $dataContent);
            // $dataContent = str_replace('<br>', '<p>', $dataContent);
            // $dataContent = str_replace('</h3>', '</h3><p>', $dataContent);
            // $dataContent = str_replace(['<strong>', '</strong>'], '', $dataContent);
            // $detail->content->textbody = $dataContent;
        // }
        // else {
        //     $detail->content->textbody = str_replace(['<strong>', '</strong>'], '', $detail->content->textbody);
        // }

        // $lists = $this->getHeadNews(1, $this->limit, $type);
        // $urlImage = $this->validate->checkLink($detail->content->image);
        // $detail->content->image = $urlImage;

        $field = '';
        if (isset($detail->fields)) {
            foreach ($detail->fields as $key => $value) {
                if ($key) {
                    $field = $key;
                }
            }
        }

        $listRelated = [];
        if ($field) {
            $listRelated = $this->filterNews(10, 1, '', $field, '', $type);
        }

        $news = $this->seo->getSeoNews($detail, $request->fullUrl());

        return view('frontend.news.detail', compact('news','lang','detail','lists','id','count','listRelated'));
    }

    public function getCountTranslate($news_id) {
        try {
            $result = $this->getTranslate($news_id);
            $count = sizeof($result['data']);
            return $count;

        } catch(\Exception $e) {
            return 0;
        }
    }

    public function getTranslate($newsId){
        try {
            $lang = Session::get('locale');
            $url   ='http://api.mazii.net/ej/api/news/'.$newsId.'/'.$lang.'/9';
            $result = json_decode(file_get_contents($url), true);
            
            return $result;
        } catch(\Exception $e) {
            return redirect()->route('error');
        }
    }

    public function translate(Request $request, $newsId) {
        $lang = $request->hl;
        $this->validate->changeLang($lang);

        $type = Session::get('type');
        $result = $this->getTranslate($newsId);
        $result = $result['data'];
        $checkUser = false;

        foreach($result as $key => $item){
            $temp = json_decode($item['content']);
            if ($temp == null) {
                $stringText = str_replace('}', ',}', $item['content']);

                preg_match_all('/"[0-9]":"(.*?)\",/', $stringText, $listText);
                preg_match_all('/"[0-9]"/', $item['content'], $listKey);

                $temp = [];

                foreach($listKey[0] as $key2 => $dataKey) {
                    $dataKey = str_replace('"', '', $dataKey);
                    $temp[$dataKey] = str_replace('"', '', $listText[1][$key2]);
                }

            } else {
                $temp = (array)$temp;
            }

            ksort($temp);

            if ( Auth::user() ) {
                if ($item['uuid'] == 'easy::'.Auth::user()->id) {
                    $checkUser = true;
                }
            }

            $result[$key]['content'] = $temp;
            $newDate = (new DateTime($item['timestamp']));
            $result[$key]['timestamp'] = $newDate->format('H:m d/m/Y');

        }

        $lists = $this->getHeadNews(1, $this->limit, $type);
        $detailNew = $this->getDetailNews($newsId);

        $title = $detailNew->title;
        $content = trim($detailNew->content->textbody);
        $str = rtrim($content, '。');
        $str = explode('。', $str);
        array_unshift($str, $title);

        $news = $this->seo->getSeoNews($detailNew, $request->fullUrl());

        return view('frontend.news.translate', compact('news', 'str','result', 'newsId', 'lists', 'detailNew', 'checkUser'));
    }

    public function getHeadNewsDay($type, $date){
        try{

            $doc = $this->filterNews(40, 1, $date, null, null, $type );

            // $arr = array(
            //     'limit' => $limit,
            //     'type' => $type,
            //     'time' => $date. ".+"
            // );

            // $postData = $this->util->postData($this->get_news_url_date, $arr);
            // $data = json_decode($postData);

            // if ($data->status == 200) {
            //     $results = $data->results;
            //     if (count($results) > 0) {
            //         $results = $this->convertData($results, $type);
            //         return $results;
            //     } else {
            //         return [];
            //     }
            // }else {
            //     return [];
            // }
        } catch(\Exception $e){
            return [];
        }
    }

    public function getHeadNews($page , $limit, $type){
		if($page == null){
			$page = 1;
        }
        try {
           
            $skip = 0;
            $limit = 10;

            $doc = $this->news->skip( $skip )->limit($limit)->getView('search', "links");
            if ($doc) {
                return $doc->rows;
            }
            // switch($type){
            //     case 'easy':
            //         $url = $this->get_news_url.$page.$limit;
            //         break;
            //     case 'normal':
            //         $url = $this->get_news_normal_url.$page.$limit;
            //         break;
            //     default:
            //         $url = $this->get_news_url.$page.$limit;
            // }
            // $postData = @file_get_contents($url);
            // if (empty($postData)) {
            //     return [];
            // } else {
            //     $data = json_decode($postData);
            //     $results = $data->results;
            //     $result = $this->convertData($results, $type);
            //     return $result;
            // }
        } catch(\Exception $e) {
            return [];
        }
    }
    
    public function convertData($results, $type) {
        foreach($results as $key => $item){
            if ($type == 'normal') {
                try {
                    $date = new DateTime($item->key);
                    $item->key = $date->format('M j, Y H:i');

                } catch (\Exception $e) {
                    $item->key = gmdate("M j, Y H:m", $item->key/1000);
                }
                $item->value->source = 'NHK';
                $item->value->image = $this->util->convertImageNormal($item->value->image);
            } else {
                $newDate = (new DateTime($item->key));
                $item->key = $newDate->format('M j, Y H:i');
               
            }

            if ( $this->validate->imageAvailable($item->value->image) ) {
                $image = $this->validate->checkLink($item->value->image);
                if ($type == 'easy') {
                    $image = $this->util->convertImageEasy($image);
                }
                $item->value->image = $image;
            }
        }
        usort($results, function($a, $b) {
            return strtotime($b->key) - strtotime($a->key);
        });
        return $results;
    }

    public function getDetailNews($newsId){
        try {
            $data = $this->news->getDoc($newsId);
            return $data;

        } catch ( Exception $e ) {
            return null;
        }

		// if( isset($data->status) && $data->status == 302){
		// 	return null;
		// } else{
        //     $newDate = (new DateTime($data->result->pubDate));
        //     $data->result->pubDate = $newDate->format('M j, Y H:m');
        //     $data->result->content->image = $this->util->convertImageNormal($data->result->content->image);
		// 	return $data->result;
		// }
    }

    public function addTranslate(Request $request) {
        try {
            $translate = $request->translate;
            $objTranslate = new \stdClass();
            foreach ($translate as $key => $value) {
                if ($value !== null) {
                    $objTranslate->$key = $value;
                } else {
                    //return with message
                    return back();
                }
            }
            $id = 'easy::'.Auth::user()->id;
            $username = Auth::user()->name;
            $news_id = $request->news_id;
            $lang = Session::get('locale');
            $arr = array(
                'uuid' => $id,
                "id" => $news_id,
                "contrycode" => $lang,
                "username" => $username,
                "content" => json_encode($objTranslate)
            );

            $postData = $this->util->postData($this->add_translate_url, $arr);
            $data = json_decode($postData);

            if($data) {
                session()->put('flash_success', trans('user.mess_add_tran_success'));
                return back();
            }else{
                session()->put('flash_danger', trans('user.mess_add_tran_fail'));
                return back();
            }
        }catch(\Exception $e){
            return null;
        }
    }

    public function getAnotherNew(Request $request) {
        $time = $request->time;
        if ($time) {
            $limit = 30;
            $type = Session::get('type') ? Session::get('type') : 'easy';
            $dateGet = ($type == 'easy') ? date('Y/m/d', strtotime($time)) : date('n/j/Y', strtotime($time));
    
            $data = $this->getHeadNewsDay($limit, $type, $dateGet);
            if ($data) {
                return Response::json($data, 200);
            } else {
                return Response::json(['message' => 'not found'], 404);
            }
        } else {
            return Response::json(['message' => 'err'], 401);
        }
    }

    public function getProverb() {
        $data = $this->util->getData($this->proverb_url);
        $proverb = new stdClass();

        if ($data && $data->todayProverbs) {
            $proverb->example = $data->todayProverbs->example;
            $proverb->pinyin = $data->todayProverbs->example_pron;
        } else {
            $proverb->example = '';
            $proverb->pinyin = '';
        }

        return $proverb;
    }

    public function filterNews($limit, $page, $date, $topic, $source, $type = 'easy') {

        $skip = $limit * ($page - 1);
        $listNews = [];

        $opts = array( "include_docs" => true, "limit" => $limit, "descending" => true, "skip" => $skip );

        try {
            if ($type == 'easy') {
                if ($source) {
                    if ($topic) {
                        //easy_source_topic_date
                        $doc = $this->getCouchdbEasyWithDate($opts, array($source, $topic, $date." 00:00"), array($source, $topic, $date." 23:59"));
                    } else {
                        //easy_source_date
                        $doc = $this->getCouchdbEasyWithDate($opts, array($source, $date." 00:00"), array($source, $date." 23:59"));
                    }
                  
                } else {
                    if ($topic) {
                        // easy_topic_date
                        $doc = $this->getCouchdbEasyWithDate($opts, array($topic, $date." 00:00"), array($topic, $date." 23:59"));
                    } else {
                        // easy_date
                        $doc = $this->getCouchdbEasyWithDate($opts, array($date." 00:00"), array($date." 23:59"));
                    }
                }
            }

            if ($type == 'normal') {
                if ($source) {
                    if ($topic) {
                        //normal_source_topic_date
                        $doc = $this->getCouchdbNormalWithDate($opts, array($source, $topic, $date." 00:00"), array($source, $topic, $date." 23:59"));
                    } else {
                        //normal_source_date
                        $doc = $this->getCouchdbNormalWithDate($opts, array($source, $date." 00:00"), array($source, $date." 23:59"));
                    }
                } else {
                    if ($topic) {
                        // normal_topic_date
                        $doc = $this->getCouchdbNormalWithDate($opts, array($topic, $date." 00:00"), array($topic, $date." 23:59"));
                    } else {
                        // normal_date
                        $doc = $this->getCouchdbNormalWithDate($opts, array($date." 00:00"), array($date." 23:59"));
                    }
                  
                }
            }

            if ($doc && $doc->rows) {
                foreach($doc->rows as $item) {
                    array_push($listNews, $item->value);
                } 
            } 

            return $listNews;

        } catch ( Exception $e ) {
            return [];
        }
    }

    public function getCouchdbEasyWithDate($opts, $key1, $key2) {
        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $key2 )
                        ->endkey( $key1 )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_EASY_MUTIPLE_DATE);
        return $doc;
    }

    public function getCouchdbNormalWithDate($opts, $key1, $key2) {
        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $key2 )
                        ->endkey( $key1 )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_NORMAL_MUTIPLE_DATE);
        return $doc;
    }
}
