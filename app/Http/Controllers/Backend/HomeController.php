<?php

namespace App\Http\Controllers\Backend;

use App\Core\Traits\Authorization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    use Authorization;


    public function index(){
        $view = view('backend.dashboard.index');
        return $view;
    }

    public function logout(){
        $this->guard()->logout();
        return redirect()->route('backend.index');
    }
}
