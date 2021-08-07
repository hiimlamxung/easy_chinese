<?php

namespace App\Http\Controllers\Backend\News;

use App\Core\Repositories\InnerNews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\ValidateController;
use Response;
use Auth;
use DateTime;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image as Image;

class NewsController extends Controller
{
    public $URL_CONVERT_PINYIN = 'https://tools.easychinese.io/pyapi/pinyin';
    public $URL_CONVERT_LEVEL = 'https://tools.easychinese.io/pyapi/seg';

    public function __construct(){
        $this->util = new UtilController();
        $this->news = new InnerNews();
        $this->valid = new ValidateController();
    }

    public function index(Request $request){
        return view('backend.news.index');
    }

    public function search(Request $request){
        $link = $request->link;
        
        if(!isset($link) || empty($link)){
            return Response::json('Link tìm kiếm rỗng.');
        }
        $count = $this->news->checkLinkDuplicate($link);  

        if($count) {
            return Response::json('Link bài báo đã tồn tại.');
        }else{
            return Response::json('Chưa tồn tại link bài báo.');
        }
    }

    public function createNews(Request $request) {
        if($request->ajax()){
            $user       = Auth::guard('admin')->user();
            $all        = Input::all();
            $title      = $all['title'];
            $pubDate    = $this->util->toDate($all['pub_date']);
            $description = $all['description'];
            $content    = str_replace(["&nbsp;", " "]," ",$all['content']);
            $link       = $all['link'];
            $video      = $all['video'];
            $nameLink   = $all['nameLink'];
            $kind       = $all['kind'];

            // Check corona in string
            if($this->valid->checkCoronaText($title) || $this->valid->checkCoronaText($description) || $this->valid->checkCoronaText($content)){
                return Response::json('New has corona text', 400);
            }

            if ($request->hasFile('image')) {
                $imageName  = $_FILES['image']['name'];
                $type       = strtolower(substr($imageName, strrpos($imageName, '.')));
                $imageName  = '/news/' . $this->util->generateRandomString(26) . str_replace('/', '', $pubDate) . $type;
                $pathImg    = public_path() . $imageName ;

                Image::make($_FILES['image']['tmp_name'])->resize(1024, 576)->save($pathImg);
            } else {
                return Response::json('Thêm ảnh bài báo!', 401);
            }

            $image = $imageName ? $imageName : '';

            if(empty($title) || empty($content)){
                return Response::json('Title or news is null', 302);
            }

            if($this->news->checkLinkDuplicate($link)) {
                return Response::json('Link is duplicate', 301);
            }

            $title = preg_replace('/<font.*?>|<\/font>/', '', $title);
            $description = preg_replace('/<font.*?>|<\/font>/', '', $description);
            $content = preg_replace('/<font.*?>|<\/font>/', '', $content);
            $content = preg_replace('/&lt;font.*?&gt;&lt;\/font&gt;/','', $content);

            $data = array(
                'title'     => preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $title)),
                'pub_date'   => $pubDate,
                'description' => preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $description)),
                'content'   => preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $content)),
                'image'     => $image,
                'link'      => $link,
                'video'     => ($video != '') ? $video : null,
                'name_link'  => $nameLink,
                'kind'      => $kind,
                'news_order' => $this->news->totalNewsForDate($pubDate) + 1
            );
           
            $create = $this->news->createNewsWeekend($user->id, $data);
            
            if($create == false){
                return Response::json('News exists', 302);
            }

            return Response::json('Success', 200);
        }
        return Response::json('Not to access', 500);
    }
    public function pubDate(Request $request){
        if($request->ajax()){
            $pubDate = $request->pub_date;
            $id = $request->id;

            $param = [
                'pub_date' => $this->util->toDate($pubDate)
            ];
            $condition = ['id' => $id];
    
            return $this->news->updateParams($param, $condition);
        }
        return Response::json('Not to access', 500);
    }

    public function editNews($id, Request $request){
        $news = $this->news->getDetailNews($id);

        $image = $news->image;
        if(!empty($image)) {
            $image = strstr($image, 'news');
        }

        if($request->ajax()){
            $all = Input::all();
            $title   = $all['title'];
            $description = $all['description'];
            $content = $all['content'];

            $content = str_replace(["&nbsp;", " "]," ",$content);
            $news->title = preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $title));
            $news->description = preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $description));
            $news->content = preg_replace('/ id=\'\d{6}\'/', '', str_replace('"', '\'', $content));

            if($news->save()) {
                return 'success';
            } else {
                return 'fail';
            }
        }

        $title = preg_replace('/<ruby.*?>(<rb>)*(.+?)<.*?<\/ruby>/', '$2', $news->title);
        $description = preg_replace('/<ruby.*?>(<rb>)*(.+?)<.*?<\/ruby>/', '$2', $news->description);
        $content = preg_replace('/<ruby.*?>(<rb>)*(.+?)<.*?<\/ruby>/', '$2', $news->content);
        
        return view('backend.news.detail', compact('news', 'id', 'title', 'content', 'description'));
    }

    public function convertPinyin(Request $request){
        if($request->ajax()){
            $str = $request->text;
            $str = preg_replace('/href=\".*?\"/', 'href="javascript:void()"', $str);
            $token = Auth::guard('admin')->user()->token;

            //add class hsk, tocfl
            $data = [
                'json' =>  [
                    "type_data" => "html",
                    "text" => $str
                ]
            ];

            $convertLevel = $this->util->postData($token, $data, $this->URL_CONVERT_LEVEL);

            if ($convertLevel && $convertLevel->seg) {
                $dataPinyin = [
                    'json' =>  [
                        "type_data" => "html",
                        "text" => $convertLevel->seg
                    ]
                ];

                $result = $this->util->postData($token, $dataPinyin, $this->URL_CONVERT_PINYIN);

                if ($result && $result->pinyin) {
                    return Response::json($result->pinyin, 200);
                } else {
                    return Response::json(null, 500);
                }
            }
            return Response::json(null, 500);
        }
        return Response::json('No access', 302);
    }

    public function newsManager($module,  Request $request){
        $title = $request->get('search');
        $date  = $request->get('fil-date');
        $with = ['admins'];
        $param = ['status' => 0];
        switch($module){
            case 'new':
                $param = ['status' => 0];
                break;
            case 'posted':
                $param = ['status' => 1];
                break;
            case 'success':
                $param = ['status' => 2];
                break;
            case 'deleted':
                $param = ['status' => -1];
                break;
            case 'all':
                $param = ['status' => ['operator' => '<>','value' => -1]];
                break;
        }

        $order = '';
        if($date != null && $date != ''){
            $date = $this->util->toDate($date);
            $param = array_merge($param, ['pub_date' => $date]);
            $order = $this->news->totalNewsForDate($date);
        }

        $user = Auth::guard('admin')->user();

        // if ($user->role == 2 && $module !== 'all') {
        //     $param['user_id'] = $user->id;
        // }
        
        $news = $this->news->getAllWithPaginate($param, $with, $page = 20, ['created_at' => 'DESC']);
        // add view short
        foreach($news as $item){
            $des_short  = substr($item->description, 0, strpos($item->description, '。'));
            $cont_short = substr($item->content, 0, strpos($item->content, '。'));
            $item->des_short  = ($des_short != '') ? $des_short : $item->description;
            $item->cont_short = ($cont_short != '') ? $cont_short : $item->content;

            $item->pub_date = $this->util->toDate2($item->pub_date);
        }

        return view('backend.news.manager', compact('news', 'module', 'order'));
    }

    public function changeStatus(Request $request) {
        $id = $request->id;
        $status = $request->status;
        $user = Auth::guard('admin')->user();

        $param = [
            'status' => $status,
            'user_post' => $user->id,
        ];
        $condition = ['id' => $id];

        return $this->news->updateParams($param, $condition);
    }
    
    public function changeOrder(Request $request){
        $id         = $request->id;
        $order      = $request->order;
        $order_old  = $request->order_old;
        $pubDate    = $request->pub_date;

        // news bị thay đổi
        $param2 = ['news_order' => $order_old];
        $condition2 = ['pub_date' => $pubDate, 'news_order' => $order];
        $second = $this->news->update($param2, $condition2);

        // news được thay đổi
        $param = ['news_order' => $order];
        $condition = ['id' => $id];
        $first = $this->news->update($param, $condition);

        if($first && $second){
            return Response::json('success', 200);
        }
        return Response::json('error', 302);
    }
}
