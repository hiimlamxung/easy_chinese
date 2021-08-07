<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Models\Code;
use App\Models\Premium;
use App\User;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CodeController extends Controller
{
    public function pick(Request $request){
        if($request->isMethod('post')){
            $day = $request->day;
            $reason = $request->reason;

            $code = Code::where('day', $day)->whereStatus(0)->first();
            if($code){
                //update code is waiting
                $code->status = 1;
                $code->reason = $reason;
                $code->save();
                return Response::json([
                    'status' => 200,
                    'result' => $code->code
                ]);
            }else{
                return Response::json([
                    'status' => 404,
                    'message' => 'Code not found'
                ]);
            }
        }
        $view = view('backend.codes.pick');
        return $view;
    }

    public function show(Request $request){
        $month = (isset($request->month)) ? $request->month : 0;
        $status = (isset($request->status)) ? $request->status : 0;

        $codes = Code::where('day', config('code.months.'.$month))
                      ->whereStatus($status)
                      ->with(['premium' => function($q){
                            $q->with('user');
                            $q->with('admin');
                      }])
                      ->orderBy('updated_at', 'DESC')
                      ->paginate(20);

        $view = view('backend.codes.index');
        $view->with('codes', $codes);
        $view->with('month', $month);
        $view->with('status', $status);
        return $view;
    }
    
    public function generate(){
        $view = view('backend.codes.generate');
        return $view;
    }

    public function create(Request $request){
        set_time_limit(0);
        $number = 1000;
        $string = '0123456789abcdefghijklmnopqrstuvwxyz';
        $length = config('code.length');
        $months = config('code.months');
        //generate code with month
        foreach($months as $month){
            $i = 0;
            while($i < $number){
                $code = UtilController::randStr($length, $string);
                if(!Code::whereCode($code)->count()){
                    $data = [
                        'code' => $code,
                        'day'  => $month
                    ];
                    Code::insert($data);
                    $i++;
                }
            }
        }
        return Response::json('Success');
    }

    public function active(Request $request){
        $code = $request->code;
        $email = $request->email;
        $id = $request->id;

        if ($email) {
            $user = User::whereEmail($email)->first();
        } elseif ($id) {
            $user = User::where("id", $id)->first();
        }
        if($user){
            //Trường hợp kích hoạt trực tiếp trên admin dashboard
            $codeObj = Code::whereCode($code)->whereStatus(0)->first();

            if($codeObj){
                DB::transaction(function() use($user, $codeObj){
                    $now = Carbon::now();
                    $time = 0;
                    if($codeObj->day) {
                        if(!empty($user->premium_expired)){
                            //Cộng dồn
                            $preTime = $user->premium_expired;
                            if($now->timestamp > $preTime){
                                $time = $now->addDays($codeObj->day)->timestamp;
                            }else{
                                $time = $preTime + 60*60*24*$codeObj->day;
                            }
                        } else {
                            $time = $now->addDays($codeObj->day)->timestamp;
                        }
                        $user->premium_expired = $time;

                    } else {
                        if(!empty($user->premium_expired)){
                            $user->premium_expired = 0;
                        }
                        $user->is_premium = true;
                    }

                    Premium::insert([
                        'user_id'  => $user->id,
                        'code'     => $codeObj->code,
                        'provider' => 'card',
                        'admin_id' => Auth::guard('admin')->user()->id,
                        'day_expired' => $time
                    ]);
                  
                    $user->save();

                    $codeObj->status = 2;
                    $codeObj->save();
                });
                return Response::json([
                    'status' => 200,
                    'message' => 'Success'
                ]);
            }else{
                return Response::json([
                    'status' => 400,
                    'message' => 'Code invalid'
                ]);
            }
        }else{
            return Response::json([
                'status' => 404,
                'message' => 'User not found'
            ]);
        }
    }
}
