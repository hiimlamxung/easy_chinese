<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidateController;
use Session;
use Config;

class SeoController extends Controller
{

    public $dataSEO = [
        'seo_home_all' => [
            'title' => [
                'vi'=> "Đọc báo tiếng Trung mỗi ngày - Easy Chinese - Todai Reader",
                'en'=> "",
                'ko'=> "",
                'zh'=> ""
            ],
            'des' => [
                'vi' => "Todai Reader - Tiếng Nhật đơn giản - Cuộc sống dễ dàng. Cập nhật tin tức Nhật Bản mới nhất, đọc báo tiếng Nhật có Fugigana và luyện nghe radio Nhật Bản. Hỗ trợ dịch báo, tra cứu nhanh, tổng hợp cấu trúc ngữ pháp, luyện thi JLPT giúp việc học tiếng Nhật thật hiệu quả",
                'en' => "",
                'ko'=> "",
                'zh'=> "Todai Reader - Easy Japanese"
            ],
            'key' => [
                'vi' => "tin tức nhật bản, đọc báo tiếng nhật, tin tức nhật bản nhk, đọc báo nhật bản,đọc báo tiếng nhật mỗi ngày, todai, báo todai tiếng Nhật, các trang web đọc báo tiếng nhật, đọc báo bằng tiếng nhật,đọc báo tiếng nhật đơn giản, đọc báo nhật đơn giản, trang đọc báo tiếng nhật, ứng dụng đọc báo tiếng nhật, nghe tin tức tiếng nhật, luyện nghe tiếng nhật qua tin tức, nghe radio tiếng nhật, nghe radio nhật bản, tin nóng nhật bản, đọc báo nhk, nhk news, đọc báo tiếng nhật nhk, đọc báo tiếng nhật cho người mới học, đọc báo tiếng nhật mỗi ngày, tin tức nhật bản mới nhất, tin tức nhật bản ngày hôm nay, tin tức bão ở nhật bản, tin tức bóng đá nhật bản, tin tức nhật bản gần đây, tin tức nhật bản hôm nay, tin tức thời sự nhật bản",
                'en' => "",
                'ko'=> "",
                'zh'=> ""
            ]
        ]
    ];

    public function setSeoHome($type, $lang, $url) {
        $data = array();
        $lang = $this->convertLang($lang);
        $image = url('frontend/images/icon-easy.png');

        $data['desc'] = $this->dataSEO[$type]['des'][$lang] ? $this->dataSEO[$type]['des'][$lang] : 'Easy News, Easy Chinese';
        $data['title'] = $this->dataSEO[$type]['title'][$lang] ? $this->dataSEO[$type]['title'][$lang] : 'Easy News | Easy Chinese';
        $data['key'] = $this->dataSEO[$type]['key'][$lang] ? $this->dataSEO[$type]['key'][$lang] : $this->dataSEO['seo_home_all']['key'][$lang];
        $data['image'] = $image;
        $data['url'] = urldecode($url);

        return $data;
    }

    public function getSeoNews($news, $url) {

        $utils = new ValidateController();
        $data = array();
        $lang = Session::get('locale');
        $topic = Config::get('topic');
        $title = '';

        if(!empty($news->description)) {
            $desc = $utils->replaceTagHTML($news->description);
            if(!empty($desc)){
                $desc = substr($desc, 0, 120);
            }
            
            $data['desc'] = $desc;
        } else {
            $data['desc'] = 'Easy News, Easy Chinese - Todai reader';
        }

        if(!empty($news->title)) {
            $title = $utils->getTitle($news->title);
            $data['title'] = $title . ' - Easy News | Easy Chinese - Todai reader';

        } else {
            $data['title'] = 'Easy News | Easy Chinese';
        }

        $keyword = $title;

        // $fields =  $news->fields;

        // foreach($fields as $key => $item) {
        //     if (isset($topic[$key])) {
        //         $keyword .= ', '. $topic[$key][$lang];
        //     }
        // }

        $data['url'] = urldecode($url);
        $data['image'] = $news->content->image;
        $data['key'] = $keyword .', news, Easy News, Easy Chinese, Todai Reader, read chinese news';

        return $data;
    }

    public function convertLang($lang) {
        if ($lang) {
            $data =  explode("-", $lang);
            return $data[0];
        } else {
            return "";
        }
    }
}
