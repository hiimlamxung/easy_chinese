<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Premium;
use App\User;
use Response;
use Carbon\Carbon;
use DB;
use Validator;

class PremiumController extends ApiController
{
    /**
     * @param string token
     * @param string device_id
     * @param string transaction
     * @param string code
     * @param string provider
     * @param int day_expired
     */
    public function active(Request $request){

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'transaction' => 'required|string',
            'code' => 'nullable|string',
            'provider' => 'required|string',
            'day_expired' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $deviceId    = $request->device_id;
        $transaction = $request->transaction;
        $code        = $request->code;
        $provider    = $request->provider;
        $dayExpired  = $request->day_expired;

        $code = strtolower($code);
        $user = $this->getUser();

        if (!in_array($provider, ['google', 'apple'])) {
            return $this->errorResponse('Provider is error!', 403);
        }
        
        if ($user) {
            switch ($provider) {
                case 'card':
                    // if ($codeObj = Code::whereCode($code)->whereStatus(1)->first()) {
                    //     try {
                    //         DB::transaction(function() use($user, $provider, $codeObj) {
                    //             $time = strtotime($codeObj->expiry_date);

                    //             Premium::insert([
                    //                 'user_id'  => $user->id,
                    //                 'code'     => $codeObj->code,
                    //                 'provider' => $provider,
                    //                 'day_expired' => $time
                    //             ]);

                    //             $user->setPremiumExpired($time, $codeObj->status_date);
                    //             $codeObj->actived();
                    //         });
                    //         return Response::json([
                    //             'status' => 200,
                    //             'result' => User::find($user->id)
                    //         ]);
                    //     } catch (Exception $e) {
                    //         return Response::json([
                    //             'status' => 500,
                    //             'message' => 'Error'
                    //         ]);
                    //     }
                    // } else {
                    //     return Response::json([
                    //         'status' => 404,
                    //         'message' => 'Code not found'
                    //     ]);
                    // };
                    break;
                case 'google':
                case 'apple':
                    if (Premium::where('device_id', $deviceId)->count()) {
                        return Response::json([
                            'status' => 403,
                            'message' => 'DeviceId is exist!'
                        ], 403);
                    } else {
                        try {
                            DB::transaction(function() use($user, $provider, $transaction, $deviceId, $dayExpired){
                                $now = Carbon::now();

                                if ($dayExpired == 0 || $dayExpired >= $user->premium_expired) {
                                
                                    Premium::insert([
                                        'user_id'       => $user->id,
                                        'transaction'   => $transaction,
                                        'provider'      => $provider,
                                        'day_expired'   => $dayExpired,
                                        'device_id'     => $deviceId,
                                    ]);
        
                                    if ($dayExpired == 0) {
                                        $user->premium_expired = 0;
                                        $user->is_premium = true;
                                        
                                    } else {
                                        $user->premium_expired = $dayExpired;
                                    }
                                    $user->save();
                                }
                            });
                            return Response::json([
                                'status' => 200,
                                'result' => User::find($user->id)
                            ]);
                        } catch(Exception $e) {
                            return Response::json([
                                'status' => 500,
                                'message' => 'Error'
                            ],500);
                        }
                    }
                    break;
            }
        } else {
            return Response::json([
                'status' => 404,
                'message' => 'User not found'
            ],404);
        }
    }
}
