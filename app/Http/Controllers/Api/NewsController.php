<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use DateTime;
use Illuminate\Http\Request;
use PHPOnCouch\CouchClient;
use Elasticsearch\ClientBuilder;
use stdClass;
use Validator;

class NewsController extends ApiController
{
    const _limit = 10;
    const _type = 'easy';
    const SEARCH_DESIGN = 'search';
    const VIEW_EASY_DATE = 'easy_by_date';
    const VIEW_NORMAL_DATE = 'normal_by_date';
    const VIEW_EASY_MUTIPLE = 'type_easy_mutiple';
    const VIEW_EASY_MUTIPLE_DATE = 'type_easy_mutiple_date';
    const VIEW_NORMAL_MUTIPLE = 'type_normal_mutiple';
    const VIEW_NORMAL_MUTIPLE_DATE = 'type_normal_mutiple_date';

    private $util;

    public function __construct()
    {
        $this->news = new CouchClient(config('couch.host'), config('couch.db.easy'));
        $this->client  = ClientBuilder::create()->setHosts([config('elasticsearch.host')])->build();
    }

    public function newsWithPage(Request $request) {

        $validator = Validator::make($request->all(), [
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric',
            'type' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $page = $request->page ? $request->page : 1;
        $limit = $request->limit ? $request->limit : self::_limit;
        $type = $request->type ? $request->type : self::_type ;

        if (!in_array($type, ['easy', 'normal'])) {
            return $this->errorResponse('Type is error!', 403);
        }

        try {
            $skip = $limit * ($page - 1);
            $options = ['limit' => $limit, 'skip' => $skip, 'descending' => true];

            if ($type == 'easy') {
                $doc = $this->news->setQueryParameters($options)->getView(self::SEARCH_DESIGN, self::VIEW_EASY_DATE);
            } else {
                $doc = $this->news->setQueryParameters($options)->getView(self::SEARCH_DESIGN, self::VIEW_NORMAL_DATE);
            }
            $news = [];

            if($doc->rows) {
                $news = $doc->rows;
                return $this->successResponse($news);
            } else {
                return $this->errorResponse('News not found', 302);
            }
        } catch ( Exception $e ) {
            return $this->errorResponse('Somehthing err !', 403);
        }
    }

    public function getDetailNews($newsId) {
        if (!$newsId) {
            return $this->errorResponse('News id is required!', 403);
        }
        try {
            $doc = $this->news->getDoc($newsId);
            $news = new stdClass();

            $news->_id = $doc->_id;
            $news->title = $doc->title;
            $news->link = $doc->link;
            $news->date = $doc->date;
            $news->tag = $doc->tag;
            $news->type = $doc->type;
            $news->content = $doc->content;
            $news->level_tocfl = $doc->level_tocfl;
            $news->level_hsk = $doc->level_hsk;
            $news->source = isset($doc->source) ? $doc->source : 'ChinaDaily';
           
            return $this->successResponse($news);

        } catch ( Exception $e ) {
            return $this->errorResponse('News id does not exist !', 404);
        }
    }

    public function filterNews(Request $request) {

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'topic' => 'nullable|string',
            'date' => 'nullable|string',
            'source' => 'nullable|string',
            'timestamp' => 'required|string',
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $today = new DateTime();
        $today = $today->format('Y-m-d');
        $topic = $request->topic; // chưa có
        $date = $request->date;
        $source = $request->source;
        $type = $request->type ? $request->type : 'easy';
        $timestamp = $request->timestamp ? $request->timestamp : $today;
        $timestamp = $timestamp . ' 23:59';

        if (!in_array($type, ['easy', 'normal'])) {
            return $this->errorResponse('Type is error!', 403);
        }

        $limit = $request->limit ? $request->limit : self::_limit;
        $page = $request->page ? $request->page : 1;
        $skip = $limit * ($page - 1);
        $listNews = [];

        $opts = array( "include_docs" => true, "limit" => $limit, "descending" => true, "skip" => $skip );

        try {
            if ($type == 'easy') {
                if ($source) {
                    if ($date) {
                        if ($topic) {
                            //easy_source_topic_date
                            $doc = $this->getCouchdbEasyWithDate($opts, array($source, $topic, $date." 00:00"), array($source, $topic, $date." 23:59"));
                        } else {
                            //easy_source_date
                            $doc = $this->getCouchdbEasyWithDate($opts, array($source, $date." 00:00"), array($source, $date." 23:59"));
                        }
                    } else {
                        if ($topic) {
                            //easy_source_topic
                            $doc = $this->getCouchdbEasy($opts, array($source, $topic), $timestamp);
                        } else {
                            //easy_source
                            $doc = $this->getCouchdbEasy($opts, array($source), $timestamp);
                        }
                    }
                } else {
                    if ($date) {
                        if ($topic) {
                            // easy_topic_date
                            $doc = $this->getCouchdbEasyWithDate($opts, array($topic, $date." 00:00"), array($topic, $date." 23:59"));
                        } else {
                            // easy_date
                            $doc = $this->getCouchdbEasyWithDate($opts, array($date." 00:00"), array($date." 23:59"));
                        }
                    } else {
                        if ($topic) {
                            // easy_topic
                            $doc = $this->getCouchdbEasy($opts, array($topic), $timestamp);
                        } else {
                            //all
                            $doc = $this->news->setQueryParameters($opts)->getView(self::SEARCH_DESIGN, self::VIEW_EASY_DATE);
                        }
                    }
                }
            }

            if ($type == 'normal') {
                if ($source) {
                    if ($date) {
                        if ($topic) {
                            //normal_source_topic_date
                            $doc = $this->getCouchdbNormalWithDate($opts, array($source, $topic, $date." 00:00"), array($source, $topic, $date." 23:59"));
                        } else {
                            //normal_source_date
                            $doc = $this->getCouchdbNormalWithDate($opts, array($source, $date." 00:00"), array($source, $date." 23:59"));
                        }
                    } else {
                        if ($topic) {
                            //normal_source_topic
                            $doc = $this->getCouchdbNormal($opts, array($source, $topic), $timestamp);
                        } else {
                            //normal_source
                            $doc = $this->getCouchdbNormal($opts, array($source), $timestamp);
                        }
                    }
                } else {
                    if ($date) {
                        if ($topic) {
                            // normal_topic_date
                            $doc = $this->getCouchdbNormalWithDate($opts, array($topic, $date." 00:00"), array($topic, $date." 23:59"));
                        } else {
                            // normal_date
                            $doc = $this->getCouchdbNormalWithDate($opts, array($date." 00:00"), array($date." 23:59"));
                        }
                    } else {
                        if ($topic) {
                            // normal_topic
                            $doc = $this->getCouchdbNormal($opts, array($topic), $timestamp);
                        } else {
                            //all
                            $doc = $this->news->setQueryParameters($opts)->getView(self::SEARCH_DESIGN, self::VIEW_NORMAL_DATE);
                        }
                    }
                }
            }

            if ($doc && $doc->rows) {
                foreach($doc->rows as $item) {
                    $item->key = isset($item->doc) ? $item->doc->date : '';
                    unset($item->doc);
                    array_push($listNews, $item);
                } 
            } 

            return $this->successResponse($listNews);

        } catch ( Exception $e ) {
            return $this->errorResponse('News id does not exist !', 404);
        }
    }

    public function searchNews(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric',
            'key' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $type  = $request->type ? $request->type : 'easy';
        $page  = $request->page ? $request->page : 1;
        $limit = $request->limit ? $request->limit : 10; 
        $key = strtolower($request->key);

        if (!in_array($type, ['easy', 'normal'])) {
            return $this->errorResponse('Type is error!', 403);
        }

        $param = [
            'index' => config('elasticsearch.index'),
            'type'  => $type,
            'size'  => $limit,
            'from'  => ($page-1)*$limit,
            '_source' => [ "_id", "title", "description", "date", "type", "source", "content.image", "content.video", "fields", "content.textmore", "tocfl", "hsk"],
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => [
                        
                        ],
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [ 'match' => [ "title" => $key ]],
                                    [ 'match' => [ "description" => $key ]],
                                    [ 'match' => [ "content.body" => $key ]],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // $cacheSearch = 'Hanzii_search_elas_'.$type.'_'.$page.'_'.$limit.'_'.$key;
        // $minutes = Carbon::now()->addDay();   // 1 day
        // if(Cache::has($cacheSearch)){
        //     return response()->json(Cache::get($cacheSearch));
        // }
        // $result = Cache::remember($cacheSearch, $minutes, function() use($param, $typeSearch, $key, $type, $language){
        try {
            $search = $this->client->search($param);
            return $this->successResponse($this->convertSearchElastic($search));
        } catch ( Exception $e ) {
            return $this->errorResponse('Something err', 404);
        }
        // });
      
    }

    public function getCouchdbEasy($opts, $key, $date) {
        $startKey = $key;
        array_push($startKey, $date);

        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $startKey )
                        ->endkey( $key )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_EASY_MUTIPLE);
        return $doc;
    }

    public function getCouchdbEasyWithDate($opts, $key1, $key2) {
        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $key2 )
                        ->endkey( $key1 )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_EASY_MUTIPLE_DATE);
        return $doc;
    }

    public function getCouchdbNormal($opts, $key, $date) {
        $startKey = $key;
        array_push($startKey, $date);

        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $startKey )
                        ->endkey( $key )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_NORMAL_MUTIPLE);
        return $doc;
    }

    public function getCouchdbNormalWithDate($opts, $key1, $key2) {
        $doc = $this->news->setQueryParameters($opts)   
                        ->startkey( $key2 )
                        ->endkey( $key1 )
                        ->getView(self::SEARCH_DESIGN, self::VIEW_NORMAL_MUTIPLE_DATE);
        return $doc;
    }

    public function convertSearchElastic($result){
        $data = $result['hits']['hits'];
        $response = [];

        foreach($data as $value) {
            $valueNews = new stdClass();
            $valueNews->id = $value['_source']['_id'];
            $valueNews->date = $value['_source']['date'];
            $valueNews->title = $value['_source']['title'];
            $valueNews->type = $value['_source']['type'];
            $valueNews->desc = $value['_source']['description'];
            $valueNews->image = $value['_source']['content']['image'];
            $valueNews->video = $value['_source']['content']['video'];
            $valueNews->source = isset($value['_source']['source']) ? $value['_source']['source'] : 'ChinaDaily';
            $valueNews->tocfl = isset($value['_source']['tocfl']) ? $value['_source']['tocfl'] : [];
            $valueNews->hsk = isset($value['_source']['hsk']) ? $value['_source']['hsk'] : [];

            $news = new stdClass();
            $news->id = $value['_source']['_id'];
            $news->key = $value['_source']['date'];
            $news->value = $valueNews;

            array_push($response, $news);
        }
        return $response;
    }
}
