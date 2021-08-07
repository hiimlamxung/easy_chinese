<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Image;
use Response;

class SaleController extends Controller
{
    public function getcountry(Request $request) {
        $country = $request->country;
        $saleOff = new Sale();

        if ($country) {
            $data = $saleOff->where('country', $country)->get();
        } else {
            $data = $saleOff->get();
        }

        $countries = $saleOff->select('id', 'country', 'active', 'sale')->get();
        $countActive = 0;

        foreach ($countries as $key => $item) {
            if ($item->active == 1) {
                $countActive ++;
            }
        }

        $checkAtive = sizeof($countries) == $countActive ? 1 : 0;

        $view = view('backend.sale_off.sale');
        $view->with('data', $data);
        $view->with('country', $country);
        $view->with('countries', $countries);
        $view->with('checkAtive', $checkAtive);

        return $view;
    }

    public function editCountry(Request $request) {
        $saleoff = $request->all();
        $saleoff['start_android'] = str_replace('T', ' ', $saleoff['start_android']);
        $saleoff['end_android'] = str_replace('T', ' ', $saleoff['end_android']);
        $saleoff['start_ios'] = str_replace('T', ' ', $saleoff['start_ios']);
        $saleoff['end_ios'] = str_replace('T', ' ', $saleoff['end_ios']);

        unset($saleoff['_token']);

        if ($request->hasFile('link_ios')) {
            $imageIos = $request->file('link_ios');
            $nameIos  = 'images/sale/_ios_' . time(). '.' . $imageIos->getClientOriginalExtension();
            $pathIos  = public_path($nameIos);
            Image::make($imageIos)->save($pathIos);

            $saleoff['link_ios'] = $nameIos;
        } 

        if ($request->hasFile('link_android')) {
            $imageAndroid = $request->file('link_android');
            $nameAndroid  = 'images/sale/_android_' . time(). '.' . $imageAndroid->getClientOriginalExtension();
            $pathAndroid  = public_path($nameAndroid);
            Image::make($imageAndroid)->save($pathAndroid);

            $saleoff['link_android'] = $nameAndroid;
        } 

        $sale = new Sale();

        if ( isset($saleoff['country']) ) {
            $saleoff['active'] = 0;
            $create = $sale->insert($saleoff);
            if ($create) {
                $mess = 'Thêm thành công.';

                return redirect()->back()->with('mess', $mess);
            } else {
                $mess = 'Quốc gia bạn thêm đã tồn tại.';

                return redirect()->back()->with('mess', $mess);
            }
        } else {
            if (isset($saleoff['lang'])) {
                $lang = $saleoff['lang'];

                unset($saleoff['lang']);

                $update = $sale->where('country', $lang)->update($saleoff);
                return back();
            } else {
                $mess = 'Chưa có quốc gia';
                return redirect()->back()->with('mess', $mess);
            }
        }
    }

    public function changeStatus(Request $request) {

        $status = $request->status;

        if ($status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        
        $country = $request->country;
        if ($country == 'all') {
            $update = Saleoff::where('id' ,'>' ,0)->update(['active' => $status]);

        } else {
            $update = Saleoff::where('country', $country)->update(['active' => $status]);
        }

        return Response::json([
            'status' => 200,
            'result' => 'Change status success'
        ],200);
    }

    public function changeAll(Request $request){
        $sale = $request->sale;
        if ($sale <= 100 ||  $sale >= 1) {
            $update = Saleoff::where('active', '!=', 1)->update(['sale' => $sale]);
        } 

        return Response::json([
            'status' => 200,
            'result' => 'Change status success'
        ],200);
    }
}
