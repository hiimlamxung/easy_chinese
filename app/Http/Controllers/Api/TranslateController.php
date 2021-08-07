<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\TranslatetRequest;
use App\Models\Translate;
use App\Models\TransLike;
use Validator;

class TranslateController extends ApiController
{
    public function getListTrans(Request $request) {
        $validator = Validator::make($request->all(), [
            'news_id' => 'required|string',
            'lang' => 'required|string|max:2',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $newsId = $request->news_id;
        $lang = $request->lang;
        $user = $this->getUser();

        $listTrans = [];
        $trans = new Translate();
        if ($user) {
            $listTrans = $trans->where('news_id', $newsId)
                                ->where('country', $lang)
                                ->with(['react' => function ($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                }])
                                ->with(array('author' => function($query) {
                                    $query->select('id','name');
                                }))
                                ->orderBy('like', 'DESC')
                                ->get();
            foreach($listTrans as $key => $item) {
                if (sizeof($item->react)) {
                    $listTrans[$key]->voted = $item->react[0]->status;
                } else {
                    $listTrans[$key]->voted = null;
                }
                unset($listTrans[$key]->react);
            }
        } else {
            $listTrans = $trans->where('news_id', $newsId)
                                ->where('country', $lang)
                                ->with(array('author' => function($query) {
                                    $query->select('id','name');
                                }))
                                ->orderBy('like', 'DESC')
                                ->get();
        }

        if($listTrans) {
            return $this->successResponse($listTrans);
        } else {
            return $this->errorResponse('Something error', 500);
        }
    }

    public function addTrans(TranslatetRequest $request) {

        $newsId = $request->news_id;
        $content = $request->content;
        $country = $request->country;
        $user = $this->getUser();
        if ($user) {
            $userId = $user->id;

            $trans = new Translate();

            $arrData = [
                "news_id" => $newsId,
                "user_id" => $userId,
                "content" => $content,
                "country" => $country
            ];

            if ($trans->where('news_id', $newsId)->where('user_id', $userId)->count()) {
                $addData = $trans->where('news_id', $newsId)
                                ->where('user_id', $userId)
                                ->update($arrData);
            } else {
                $addData = $trans->insert($arrData);
            }

            if($addData) {
                return $this->successResponse('Success');
            } else {
                return $this->errorResponse('Something error', 500);
            }
        } else {
            return $this->errorResponse("Authenticated faild", 401);
        }
    }

    public function addReact(Request $request) {
        $validator = Validator::make($request->all(), [
            'trans_id' => 'required|numeric',
            'status' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $transId = $request->trans_id;
        $status = $request->status;
        $user = $this->getUser();
        if ($user) {
            $userId = $user->id;
            $trans = new TransLike();
    
            $react = Translate::where('id', $transId)->first();
            $data = $trans->where('trans_id', $transId)->where('user_id', $userId);
          
            if ($data->count()) {
                $oldData = $data->first();
    
                if ( $oldData->status != $status ) {
                    $addData = $trans->where('trans_id', $transId)
                                    ->where('user_id', $userId)
                                    ->update(['status' => $status]);
    
                    if ($status) {
                        $react->increment('like');
                        $react->decrement('dislike');
                    } else {
                        $react->increment('dislike');
                        $react->decrement('like');
                    }
                }
                return $this->successResponse('Success');
            } else {
                $arr = [
                    "trans_id" => $transId,
                    "user_id" => $userId,
                    "status" => $status
                ];
    
                $addData = $trans->insert($arr);
    
                if ($status) {
                    $react->increment('like');
                } else {
                    $react->increment('dislike');
                }
    
                return $this->successResponse('Success');
            }
            return $this->errorResponse('Something error', 500);
        } else {
            return $this->errorResponse("Authenticated fail", 401);
        }
    }
}
