<?php

namespace App\Traits;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ApiResponser{
    protected function successResponse($data, $code = 200){
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code){
        return response()->json([
            'error' => $message,
            'code'  => $code
        ], $code);
    }

    protected function showAll(Collection $collection, $code = 200){
        if($collection->isEmpty()){
            return $this->successResponse([
                'result' => $collection
            ], $code);
        }

        return $this->successResponse([
            'result' => $collection
        ], $code);
    }

    protected function showOne(Model $model, $code = 200){
        return $this->successResponse([
            'result'   => $model
        ], $code);
    }

    protected function unAuthorize($model){
        throw new HttpException(422, "The specified {$model} is not the actual {$model} of the user");
    }

    protected function unUpdate(){
        return $this->errorResponse('You need to specify any different value to update', 422);
    }

    protected function getUser(){
        $token = request()->header('Authorization');
        if(empty($token)){
            return null;
        }
        return User::where('remember_token', $token)->first();
    }

    protected function cacheResponse($data){
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";
        
        return Cache::remember($fullUrl, 30/60, function() use($data){
            return $data;
        });
    }
}