<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = Session::get('locale');
        if (!$lang) {
            $lang_browser = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : '';

            if (in_array($lang_browser, ['ko-KR', 'vi-VN', 'zh-CN', 'zh-TW'])) {
                $lang = $lang_browser;
            } else {
                $lang = 'en-US';
            }
        }
        Session::put('locale', $lang);

        App::setLocale($lang);

        return $next($request);
    }
}
