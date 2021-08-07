<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Response;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->comment = new Comment();
    }

    public function addComment(Request $request) {
        if ($request->ajax()) {
            $message = $request->message;
            $news = $request->news;
            $error = $request->error;
            $user = Auth::guard('admin')->user();

            $data = [
                'user_id' => $user->id,
                'news_id' => $news,
                'errors' => $error,
                'comment' => $message,
                'reading' => 0
            ];

            $result = $this->comment->create($data);

            if ($result) {
                return Response::json($result, 200);
            } else {
                return Response::json('Errors', 500);
            }
        } else {
            return Response::json('Không có quyền truy cập!', 302);
        }
    }
}
