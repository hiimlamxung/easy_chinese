<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\ValidateController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Session;
use Socialite;
use Validator;
use Config;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use AuthenticatesUsers;
    

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('web', ['except' => 'logout']);
        $this->valid = new ValidateController();
        $this->user = new User();
        $this->util = new UtilController();
    }

    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider, Request $request){
        $userSocial = Socialite::driver($provider)->user();

        $users = User::where(['email' => $userSocial->getEmail()])->first();

        if ($users) {
            if (!$users->provider) {
                $users->image = $userSocial->getAvatar();
                $users->provider_id = $userSocial->getId();
                $users->provider = $provider;
                $users->save();
            }

            if (!$users->remember_token) {
                $users->remember_token  = $this->util->generateRandomString(Config::get('constants.token'));
                $users->save();
            }
            
            Auth::login($users);
        } else {
            $newUser = User::create([
                    'name'          => $userSocial->getName(),
                    'email'         => $userSocial->getEmail(),
                    'image'         => $userSocial->getAvatar(),
                    'provider_id'   => $userSocial->getId(),
                    'status'        => 1,
                    'provider'      => $provider,
                    'language'      => Session::get('locale'),
                    'remember_token'  => $this->util->generateRandomString(Config::get('constants.token'))
                ]);
            Auth::login($newUser);
        }
        return redirect()->route('web.home');
    }

    public function login(Request $request) {
        $lang = $request->hl;
        $this->valid->changeLang($lang);

        if ($request->isMethod('post')) {
            $email = $request->email;
            $password = $request->password;
            $validatorArray = [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ];
    
            $validator = Validator::make($request->all(), $validatorArray);

            if ($validator->fails()) {
                $message = $validator->errors();
                return view('frontend.user.login')->with('message', $message->first());
            }

            if ( Auth::guard('web')->attempt(['email' => $email, 'password' => $password], true) ) {
                session()->put('flash_success', trans('user.mess_login_success'));
                return redirect( route('web.home') );
            } else {
                return view('frontend.user.login')->with(['message' => trans('user.mess_login') ]);
            }
        }
        return view('frontend.user.login');
    }

    public function register(Request $request) {
        $lang = $request->hl;
        $this->valid->changeLang($lang);

        if ($request->isMethod('post')) {

            $email      = (isset($request->email)) ? $request->email : '';
            $password   = (isset($request->password)) ? $request->password : '';
            $confirm_password  = (isset($request->confirm_password)) ? $request->confirm_password : '';
    
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                return view('frontend.user.register')->with('message', $message->first());
            }

            if ($email != '' && $password != '') {
                $dataName = explode('@', $email);
                $name = $dataName[0];
                $data = [
                    'email' => $email,
                    'password' => bcrypt($password),
                    'name' => $name,
                    'status' => 1,
                    'language'  => Session::get('locale'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $create = $this->user->registerUser($data);

                if ($create == false) {
                    return view('frontend.user.register')->with('message', trans('user.mess_exist_email'));
                } else {
                    $login = Auth::guard('web')->attempt(['email' => $email, 'password' => $password, 'status' => 1], true);
                    session()->put('flash_success', trans('user.mess_register_success'));
                    return redirect()->route('web.home');
                }
                
            } else {
                return view('frontend.user.register')->with('message', trans('user.mess_err_input'));
            }
        }

        return view('frontend.user.register');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('web.home');
    }
    
    public function profile(Request $request) {
        return view('frontend.user.profile');
    }

    public function upload(Request $request) {
        $folderPath = public_path('files/images/');

        $image_parts = explode(";base64,", $request->image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $filename = '/files/images/_' . substr(md5('_' . time()), 0, 15) .'.png';
        $path = public_path($filename);

        file_put_contents($path, $image_base64);

        $user = Auth::user();
        $user->image = url($filename);
        $user->save();

        return response()->json(['message'=> trans('user.change_avatar_success'), 'src' => url($filename)]);
    }

    public function updateProfile(Request $request) {
        $name     = $request->username;
        $level    = $request->level;
        
        if (!$name) {
            return view('frontend.user.login')->with('message', 'Invalid username');
        }

        $user = Auth::user();
        $user->name = $name;
        $user->level = $level;
        $user->save();
        session()->put('flash_success', trans('user.mess_update_user_success'));
        return back();

    }
}
