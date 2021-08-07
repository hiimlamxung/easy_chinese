<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Sale;
use Illuminate\Http\Request;
use stdClass;
use Validator;

class SaleController extends ApiController
{
    public $BASE_URL = '//easychinese.io/';

    public function getSaleoff(Request $request) {
        $validator = Validator::make($request->all(), [
            'version' => 'required|string',
            'lang' => 'nullable|string|max:2',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 403);
        }

        $lang = $request->lang;
        $version = $request->version;

        if (!$lang) {
            $lang = 'en';
        } else {
            $count = Sale::where('country', $lang)->count();
            if ($count == 0) {
                $lang = 'en';
            }
        }

        $sale = Sale::where('country', $lang)->first();

        if($sale) {
            if ($sale->link_ios) {
                $sale->link_ios = $this->BASE_URL.$sale->link_ios;
            }

            if ($sale->link_android) {
                $sale->link_android = $this->BASE_URL.$sale->link_android;
            }

            $data = new stdClass();
            $data->country = $sale->country;
            $data->sale = $sale->sale;
            $data->version = $sale->version;
            $data->title = $sale['title_' .$version];
            $data->description = $sale['description_' .$version];
            $data->link = $sale['link_' .$version];
            $data->active = $sale->active;
            $data->start = $sale['start_' .$version];
            $data->end = $sale['end_' .$version];

            return $this->successResponse($data);
        } else {
            return $this->errorResponse('Something error', 500);
        }
    }
}
