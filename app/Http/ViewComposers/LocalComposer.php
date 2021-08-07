<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Session;

class LocalComposer
{
    public function __construct() {
    }

    public function compose(View $view) {
        $lang = Session::get('locale') ? Session::get('locale') : 'vi-VN';

        $view->with('lang', $lang);
    }
}