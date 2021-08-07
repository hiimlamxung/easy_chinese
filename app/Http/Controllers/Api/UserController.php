<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\ValidateController;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Config;
use Image;
use Validator;

class UserController extends ApiController
{
    private $user;
    private $vali;
    private $util;

    public function __construct()
    {
        $this->user = new User();
        $this->util = new UtilController();
        $this->vali = new ValidateController();
    }

    public function randomUsersPremium() {
        $users = User::premium();
        $result = [
            'total' => $users->count(),
            'users' => $users->get(['name', 'image'])->random(100)
        ];

        return response($result);
    }

    public function editProfile(Request $request){
        $language = $request->language;
        $name = $request->name;
        $hsk = $request->level_hsk;
        $tocfl = $request->level_tocfl;

        $user = $this->getUser();

        if ($user) {
            if (!empty($language)) {
                $user->language = $language;
            }

            if (!empty($name)) {
                $user->name = $name;
            }

            if (!empty($hsk)) {
                $user->level_hsk = (int)$hsk;
            }

            if (!empty($tocfl)) {
                $user->level_tocfl = (int)$tocfl;
            }

            if ($user->save()) {
                $user->is_premium = (string)$user->is_premium;
                $user->premium_expired = (string)$user->premium_expired;
                $user->level_hsk = (int)$user->level_hsk;
                $user->level_tocfl = (int)$user->level_tocfl;

                return $this->successResponse($user);
            } else {
                return $this->errorResponse('Something error', 500);
            }
        } else {
            return $this->errorResponse('User not found', 404);
        }
    }

    public function editImage(Request $request) {

        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'option' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $this->getUser();
        $image = $request->image;
        $option = $request->option;


        /**
         * option = link
         * option = image
         * option = base64
         */

        if (!in_array($option, ['link', 'file', 'base64'])) {
            return $this->errorResponse('Option incorrect', 400);
        }
        if ($user) {
            switch($option) {
                case 'link':
                    if ($user->setImage($image)) {
                        return $this->successResponse($user->image);
                    } else {
                        return $this->errorResponse('Something err', 500);
                    }
                    break;
                case 'file':
                    $image = $request->file('image');
                    $name  = 'Thumb_' . time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                    $path  = public_path('/images/avatars/'.$name);
                    Image::make($_FILES['image']['tmp_name'])->save($path);
                    $user->setImage(Config::get('app.url').'/images/avatars/'.$name);

                    return $this->successResponse($user->image);
                    break;
                case 'base64':
                    $image = str_replace('data:image/png;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $name = 'Thumb_' . time() . '_' . $user->id . '.' . 'png';
                    \File::put(public_path('/images/avatars/'.$name), base64_decode($image));
                    $user->setImage(Config::get('app.url').'/images/avatars/'.$name);

                    return $this->successResponse($user->image);
                    break;
            }
            return $this->errorResponse('Image not found', 404);
        } else {
            return $this->errorResponse('User not found', 404);
        }
    }

    public function loginWithSocial(Request $request) {
        $accessToken = $request->access_token;
        $provider    = $request->provider;
        $idToken     = $request->id_token;
        $language    = $request->language;

        if ($provider == 'facebook') {
            $url = "https://graph.facebook.com/v2.6/me?fields=id,name,email,picture,birthday&access_token={$accessToken}";

        } else if ($provider == 'google') {
            if (isset($idToken) && !empty($idToken)) {
                $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$idToken";
                $accessToken = $idToken;
            } else {
                $url = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=$accessToken";
            }
        }

        $info = $this->util->getData($url);

        if (!isset($info->error)) {
            $data = [];
            switch($provider) {
                case 'facebook':
                    $data = [
                        'provider'    => 'facebook',
                        'provider_id' => $info->id,
                        'name'        => $info->name,
                        'email'       => isset($info->email) ? $info->email : '',
                        'image'       => 'https://graph.facebook.com/'.$info->id.'/picture?type=normal'
                    ];
                    break;
                case 'google':
                    $data = [
                        'provider'    => 'google',
                        'provider_id' => $info->sub,
                        'name'        => $info->name,
                        'email'       => $info->email,
                        'image'       => $info->picture
                    ];
                    break;
            }

            if ($provider == 'google') { 
                $user = $this->user->where(['email' => $data['email']])->first();
            } else {
                $user = $this->user->where(['provider_id' => $data['provider_id']])->first();
            }

            if ($user) {
                $user->status = 1;
                $user->save();
                $user->is_premium = (string)$user->is_premium;
                $user->premium_expired = (string)$user->premium_expired;
                
                return $this->successResponse($user);
            } else {
                //Create new user
                $data['status'] = 1;
                $data['language'] = $language;
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['remember_token']  = $this->util->generateRandomString(Config::get('constants.token'));
                $user = $this->user->insert($data);

                if ($user) {
                    $user = User::where("remember_token", $data['remember_token'])->first();
                    $user->is_premium = (string)$user->is_premium;
                    $user->premium_expired = (string)$user->premium_expired;

                    return $this->successResponse($user);
                } else {
                    return $this->errorResponse('Login err', 502);
                }
            }
        } else { 
            return $this->errorResponse('Login err', 302);
        }
    }

    /**
     * login with apple
     * @param string $email
     * @param string $name
     * @param string $token
     * @param string $user
     * @param string $lang
     */
    public function loginWithApple(Request $request){
        $email    = $request->email;
        $name    = $request->name;
        $token = $request->token;
        $apple_user = $request->apple_user;
        $lang =  $request->language;

        if($apple_user){
            // If exits account
            $user = User::where('apple_user', $apple_user)->first();
            if($user) {
                $user->status = 1;
                $user->save();
                $user->is_premium = (string)$user->is_premium;
                $user->premium_expired = (string)$user->premium_expired;

                return $this->successResponse($user);
            } else {
                // If exists email
                if(!$email && $user = User::whereEmail($email)->first()) {
                    $user->apple_token = $token;
                    $user->apple_user = $user;
                    $user->status = 1;
                    $user->save();
                    $user->is_premium = (string)$user->is_premium;
                    $user->premium_expired = (string)$user->premium_expired;

                    return $this->successResponse($user);
                }
                // Create new user
                $new = [
                    'name'  => $name,
                    'email'     => $email,
                    'apple_token' => $token,
                    'apple_user'  => $apple_user,
                    'status'    => 1,
                    'language'  => $lang,
                    'remember_token' => $this->util->generateRandomString(Config::get('constants.token'))
                ];

                $newUser = $this->user->insert($new);
                
                if($newUser) {
                    $user = User::where('apple_user', $apple_user)->first();
                    $user->is_premium = (string)$user->is_premium;
                    $user->premium_expired = (string)$user->premium_expired;

                    return $this->successResponse($user);
                }
            }
        }

        return $this->errorResponse('Login err', 302);
    }

    public function getProfile(Request $request) {

        $token = request()->header('Authorization');

        if(empty($token)){
            return $this->errorResponse('Token is required', 400);
        }

        if ($user = $this->user->where(['remember_token' => $token,'status' => 1])->first()) {
            $user->is_premium = (string)$user->is_premium;
            $user->premium_expired = (string)$user->premium_expired;

            return $this->successResponse($user);
        } else { 
            return $this->errorResponse('Token sai hoặc user chưa đăng nhập', 400);
        }
    }

    public function changePassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 403);
        }

        $password = $request->password;
        
        if($user = $this->getUser()){
            $user->password = bcrypt($request->password);
            if ($user->save()) {
                return $this->successResponse('Change password success.');
            }
            return $this->errorResponse('Error update', 400);
        }else{
            return $this->errorResponse('User not found', 400);
        }
    }

    public function logout(Request $request) {
        $token = request()->header('Authorization');

        if(empty($token)){
            return $this->errorResponse('Token is required', 400);
        }

        if ($this->user->where('remember_token', $token)->update(['status' => 0])) {
            return $this->successResponse('Logout success');
        } else {
            return $this->errorResponse('Token err.', 400);
        }
    }

    public function loginWithEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 403);
        }

        $email    = $request->email;
        $password = $request->password;

        $user = $this->user->where([
            'email'    => $email,
        ])->first();

        if ($user) {
            $checkUser = Hash::check($password, $user->password, []);
            
            if ($checkUser) {
                $user->status = 1;
                $user->save();
                $user->is_premium = (string)$user->is_premium;
                $user->premium_expired = (string)$user->premium_expired;
                
                return $this->successResponse($user);
            } else {
                return $this->errorResponse('Email hoặc mật khẩu không đúng.', 400);
            }
      
        } else {
            return $this->errorResponse('Email hoặc mật khẩu không đúng.', 400);
        }
    }

    public function register(RegisterRequest $request) {
        $email    = $request->email;
        $password = $request->password;
        $language = $request->language;
        $name = $request->name;

        if ($this->vali->valEmail($email) && !empty($password)) {
            //check exist email
            $user = [
                'email'    => $email,
                'name'     => $name ? $name : substr($email, 0, strpos($email, '@')),
                'remember_token'  => $this->util->generateRandomString(Config::get('constants.token')),
                'password' => bcrypt($password),
                'status'   => 0,
                'language' => $language,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $add = $this->user->insert($user);
            if ($add) {
                $newUser = $this->user->where('email', $email)->first();
                $newUser->status = 1;
                $newUser->save();
                $newUser->is_premium = (string)$newUser->is_premium;
                $newUser->premium_expired = (string)$newUser->premium_expired;
                
                return $this->successResponse($newUser);
            } else {
                return $this->errorResponse('Server error!', 500);
            }

        } else {
            return $this->errorResponse('Email wrong or password empty.', 401);
        }
    }
}
