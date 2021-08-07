<?php

namespace App\Core\Traits;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

trait Authorization {

    use AuthenticatesUsers;

    /**
     * @return guard
     */
    public function guard(){
        return Auth::guard('admin');
    }
}