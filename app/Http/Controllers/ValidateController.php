<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App;

class ValidateController extends Controller
{
    private $listLang = ['vi-VN', 'en-US', 'ko-KR', 'zh-CN', 'zh-TW'];

    public function getVideo($video){
		$object = '<object type="application/x-shockwave-flash" data="http://www3.nhk.or.jp/news/player5.swf" class="movie-news-sm movie-news-md" id="news_image_div3" style="visibility: visible;">
            <param name="allowScriptAccess" value="sameDomain">
            <param name="allowFullScreen" value="true">
            <param name="wmode" value="direct">
            <param name="quality" value="high">
            <param name="bgcolor" value="#000000"> 
            <param name="flashvars" value="fms=rtmp://flv.nhk.or.jp/ondemand/flv/news/&amp;movie='.$video.'"></object>';
        return $object;
    }
    
    public function checkCoronaText($string){
        $regex = '/(疫情|新型冠状病毒|肺炎疫情|确诊病例|死亡病例|接触者|重症病例|疑似病例|接种|疫苗|冠状病毒疫苗接种|新型冠状病毒|冠狀)/';
        return preg_match_all($regex, $string);
    }

	public function videoAvailable($video){
		if($video == null || $video == ''){
			return false;
		}
		return true;
	}

	public function imageAvailable($image){
		if($image == null || $image == ''){
			return false;
		}
		return true;
	}

    public function replaceTagHTML($str){
        $result = '';
        if(!empty($str)){
            $result = preg_replace('/(<\/?p>)|(<\/?ruby>)|(<\/?rt>)|(「)|(」)/', '', $str);
            $result = strip_tags($result, 'a');
        }

        return $result;
    }
    public function getTitle($str)
    {
        $result = '';
        if(!empty($str)){
            $result = preg_replace('/<rt>(.*?)<\/rt>/', '', $str);
            $result = strip_tags($result, 'a');       
        }
        return $result;
    }

    function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

    public function valueNotNull($value){
        if($value == '' || $value == null)
            return false;
        return true;
    }

    public function valEmail($email) {
		if ($email == null || $email == '')
			return false;

		if(preg_match('/\A[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}\z/', $email)
        && preg_match('/^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/', $email)){
			return true;
		}else
			return false;
    }
    
    public function changeLang($lang) {
        if (!$lang) {
            $lang = Session::get('locale') ? Session::get('locale') : 'en-US';
        }

        if (!in_array($lang, $this->listLang)) {
            $lang = 'en-US';
        }
      
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
}
