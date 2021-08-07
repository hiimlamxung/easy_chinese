<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;

class UserController extends Controller
{
    public function getAllUser(Request $request) {

        $search = $request->search;
        if(!empty($search)){
            $users = User::where(function($q) use ($search){
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            })->paginate(20);
        }else{
            $users = User::paginate(20);
        }

        return view('backend.users.index', compact('users'));
    }

    public function changePass(Request $request) {
        $id = $request->id;
        $password = $request->password;

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $user = User::find($id);
        if ($user) {
            $user->password = bcrypt($password);
            $user->save();
        }

        return back()->withErrors("Đổi mật khẩu thành công!");
    }
}
