<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admins\Admin;
use App\Models\Comment;
use Auth;

class StatisticalController extends Controller
{
     // Các quyền user
    // 0: kiểm duyệt viên
    // 1: admin 
    // 2: ctv
    public function __construct()
    {
        $this->user = new Admin();
        $this->comment = new Comment();
    }

    public function readNoti(Request $request)
    {
        if ($request->ajax()) {
            $news = $request->news;
            // Cập nhật trạng thái đã đọc lỗi
            $update = $this->comment->where(['news_id' => $news, 'errors' => 1])->update(['reading' => 1]);
            if ($update) {
                return Response::json('success', 200);
            } else {
                return Response::json('error', 500);
            }
        } else {
            return Response::json('Not access', 302);
        }
    }

    public function censorship(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $id = $request->id;

        if (!isset($month) || empty($month)) {
            $month = date('m');
        }
        if (!isset($year) || empty($year)) {
            $year = date('Y');
        }
        if (!isset($id) || empty($id)) {
            $user = Auth::guard('admin')->user();
        } else {
            $user = Admin::find($id);
        }

        $listCensor = Admin::where('role', 3)->where('active', 1)->get();
        $news = $this->user->with(['actionNews' => function ($q) use ($month, $year) {
                            $q->with(['comments' => function ($c) {
                                $c->where('errors', 1);
                            }]);
                            $q->where('pub_date', 'like', (int)$month . '%' . (int)$year);
                            $q->orderBy('created_at', 'DESC');
                        }])->where('id', $user->id)->first();
        // Tính tiền cho CTV
        // Các bài được duyệt không lỗi: 5000 VNĐ
        // Các bài được duyệt có lỗi: 10000 VNĐ
        // Bài đã phát hành có lỗi: -20000 VNĐ
        $price = 5000;

        $statis = [
            'total' => count($news['actionNews']),
            'success' => 0,
            'posted' => 0,
            'deleted' => 0,
            'error' => 0,
            'offer' => 0,
            'price' => 0
        ];

        foreach ($news['actionNews'] as &$new) {
            $new->price = 0;
            $new->donate = 0;
            switch ($new->status) {
                case -1:
                    $statis['deleted']++;
                    break;
                case 1:
                    $statis['posted']++;
                    $new->price = $price;
                    break;
                case 2:
                    $statis['success']++;
                    $statis['price'] += $price;
                    $new->price = $price;
                    break;
            }
            if (count($new['comments'])) {
                $statis['error'] += 1;
                $new->donate = $price;
                $statis['price'] += $price;
            }
            $new->price += $new->donate;
        }

        //Thưởng cuối tháng
        if ($statis['total'] != 0 && $statis['success']) {
            if ((($statis['success'] - $statis['error']) / $statis['success']) * 100 >= 70) {
                $statis['offer'] = (int)($statis['price'] * 0.1);
            }
        }


        $view = view('backend.statistical.censorship');
        $view->with('statis', $statis);
        $view->with('news', $news);
        $view->with('price', $price);
        $view->with('month', $month);
        $view->with('year', $year);
        $view->with('user', $user);
        $view->with('listCensor', $listCensor);

        return $view;
    }

    public function collaborators(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $id = $request->id;

        if (!isset($month) || empty($month)) {
            $month = date('m');
        }
        if (!isset($year) || empty($year)) {
            $year = date('Y');
        }

        if (!isset($id) || empty($id)) {
            $user = Auth::guard('admin')->user();
        } else {
            $user = Admin::find($id);
        }

        $listCTV = Admin::where('role', 2)->where('active', 1)->get();

        $news = $this->user->with(['news' => function ($q) use ($month, $year) {
                                $q->with(['comments' => function ($c) {
                                    $c->where('errors', 1);
                                }]);
                                $q->where('pub_date', 'like', '%' .$month . '/' . $year);
                                $q->orderBy('created_at', 'DESC');
                            }])->where('id', $user->id)->first();


        // Tính tiền cho CTV
        // Các bài được duyệt: 20000 VNĐ
        // Bài được duyệt có từ 3 -> 5 lỗi: - 10%
        // Bài được duyệt có trên 5 lỗi: - 20%
        // Bài đúng hoàn toàn: + 10%
        // Cuối tháng Số bài đúng/Tổng bài phát hành > 90%: + 10% của tháng
        $price = 20000;

        $statis = [
            'total' => count($news['news']),
            'success' => 0,
            'new' => 0,
            'posted' => 0,
            'deleted' => 0,
            'error' => 0,
            'perfect' => 0,
            'offer' => 0,
            'price' => 0,
            'newsErr' => []
        ];

        foreach ($news['news'] as $new) {
            $new->price = 0;
            $new->sale = 0;
            switch ($new->status) {
                case -1:
                    $statis['deleted']++;
                    break;
                case 0:
                    $statis['new']++;
                    break;
                case 1:
                    $statis['posted']++;
                    $new->price = $price;
                    break;
                case 2:
                    $statis['success']++;
                    $statis['price'] += $price;
                    $new->price = $price;
                    break;
            }
            if (count($new['comments'])) {
                $statis['error'] += 1;
                $statis['newsErr'][] = $new;
            } elseif (count($new['comments']) == 0 && $new->status > 0) {
                $statis['perfect']++;
            }
            if (count($new['comments']) >= 3 && count($new['comments']) <= 5) {
                if ($statis['price'] > 0) {
                    $statis['price'] -= ($price * 0.1);
                }
                if ($new->price > 0) {
                    $new->sale = ($price * 0.1);
                }
            }
            if (count($new['comments']) > 5) {
                if ($statis['price'] > 0) {
                    $statis['price'] -= ($price * 0.2);
                }
                if ($new->price > 0) {
                    $new->sale = ($price * 0.2);
                }
            }
            $new->price -= $new->sale;
        }

        // Thưởng cuối tháng
        if ($statis['perfect'] != 0 && $statis['success'] != 0) {
            if ($statis['perfect'] / ($statis['success'] + $statis['posted']) * 100 > 69) {
                $statis['offer'] = (int)($statis['price'] * 0.1);
            }
        }

        $view = view('backend.statistical.ctv');
        $view->with('statis', $statis);
        $view->with('news', $news);
        $view->with('price', $price);
        $view->with('month', $month);
        $view->with('year', $year);
        $view->with('user', $user);
        $view->with('listCTV', $listCTV);

        return $view;
    }
}
