<?php

if(!function_exists('remove_newlines')){
    /**
     * Remove newlines in string
     * 
     * @return string
     */
    function remove_newlines($string){
        return trim(preg_replace('/\s+/', ' ', $string));
    }
}

if(!function_exists('random_string')){
    /**
     * Generate string 
     * 
     * @return string
     */
    function random_string($length = 32){
        return substr(md5(mt_rand()), 0, $length);
    }
}

if(!function_exists('curl_post')){
    /**
     * Get data via curl
     * 
     * @return object
     */
    function curl_post($url, $data){
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'Accept: */*'
		));
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
    }
}

if(!function_exists('curl_get')){
    /**
     * Get data via curl
     * 
     * @return object
     */
    function curl_get($url){
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'Accept: */*'
		));
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
    }
}

if(!function_exists('is_chinese')){
    /**
     * Check string is chinese
     * 
     * @return boolean
     */
    function is_chinese($char){
        return preg_match('/\p{Han}+/u', $char);
    }
}

if(!function_exists('push_fcm')){
    /**
     * Check string is chinese
     * 
     * @return boolean
     */
    function push_fcm($title, $total, $newsID, $type = 'easy'){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to' => "/topics/$type-news",
            'notification'   => array(
                "title"  => $title,
            ),
            'data' => array(
                'newsID' => $newsID,
                'total' => $total,
                'type' => $type
            )
        );
        $headers = array(
            'Authorization: key=' . config('app.fcm_key'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
