<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use GuzzleHttp\Client;

class UtilController extends Controller
{

    public function encodeWithUni($string){
        return json_encode($string, JSON_UNESCAPED_UNICODE);
    }

    public static function randStr($length, $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
	    $charactersLength = strlen($string);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $string[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
    }
    
    public function generateRandomNumber($length) {
	    $characters = '0123456789';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
    }
    
    public function generateRandomString($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    // convert 0x/0y/yyyy => x/y/yyyy
    public function toDate($date){
        $arr = explode('-', $date);
        $result = $arr[2] . '/' . $arr[1] . '/' . $arr[0];

        return $result;
    }

    // convert x/y/yyyy => yyyy-mm-dd
    public function toDate2($date) {
        $arr = explode('/', $date);

        $result = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        return $result;
    }
    // make query string
    public function create_link($uri, $filter = array())
    {
        $string = '';
        foreach ($filter as $key => $val)
        {
            $string .= "&{$key}={$val}";
        }
        return $uri . ($string ? '?'.ltrim($string, '&') : '');
    }

    // pagination function
    public function paging($link, $total_records, $current_page, $limit, $keyword = '')
    {
        $range = 10;
        $min   = 0;
        $max   = 0;

        $total_page = ceil($total_records / $limit);
        
        if($current_page > $total_page){
            $current_page = $total_page;
        }elseif($current_page < 1){
            $current_page = 1;
        }

        $middle = ceil($range/2);
        if($total_page < $range){
            $min = 1;
            $max = $total_page;
        }else{
            $min = $current_page - ($middle + 1);
            $max = $current_page + ($middle - 1);

            if($min<1){
                $min = 1;
                $max = $range;
            }elseif($max > $total_page){
                $max = $total_page;
                $min = $total_page - $range + 1;
            }
        }

        $start = ($current_page -1)*$limit;
        $html = "<div class='text-center";
        $html .= "<nav aria-label='Page navigation'>";
        $html .= "<ul class='pagination'>";

        if($current_page > 1 && $max > 1){
            $html .= '<li><a href="'.$link . '/'.($current_page - 1) .'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        for($i=$min; $i<=$max; $i++){
            if($i == $current_page){
                $html .= '<li class="active"><a>'.$i.'<span class="sr-only"></span></a></li>';
            }else{
                $html .= '<li><a href="'. $link .'/'. $i . '">'.$i.'<span class="sr-only"></span></a></li>';

            }
        }

        if($current_page < $max && $max > 1){
            $html .= '<li><a href="' . $link .'/'. ($current_page+1) . '"aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
        $html .= "</ul>";
        $html .= "</nav>";
        $html .= "</div>";
        // Trả kết quả
        return array(
            'start' => $start,
            'limit' => $limit,
            'key'  => $keyword,
            'html' => $html
        );
    }

    public function getData($url, $token = '') {

        $client = new Client(["headers" => [
                                "Accept" => "application/json",
                                'Authorization' => $token
                                ]
                            ]);
        $request = $client->get($url);
        return json_decode($request->getBody());
    }

    public function postData($token = '', $data, $url) {
        $client = new Client([
            "headers" => [
                "Accept" => "application/json",
                "Authorization" => $token
            ]
        ]);
        $request = $client->request('POST', $url, $data);

        $res = $request->getBody()->getContents();
        $res = json_decode($res);

        return $res;
    }
}
