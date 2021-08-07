<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Core\Traits\Authorization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;

class LoginController extends Controller
{
    use Authorization;
    
    public function index(){
        $view = view('backend.auth.login');
        return $view;
    }

    public function login(LoginRequest $request){
        $params = $request->only('email', 'password', 'active');
        if($this->guard()->attempt($params)){
            return redirect()->route('backend.dashboard');
        }
        return redirect()->back()->withErrors(['msg' => 'Account does not have access!']);
    }
}
