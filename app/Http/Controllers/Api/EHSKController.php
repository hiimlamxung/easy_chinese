<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilController;
use App\Models\EHistory;
use App\Models\EPartWrite;
use stdClass;
use Validator;

class EHSKController extends ApiController
{

    public $BASE_URL = "http://api.hanzii.net/api/gethsk/hskbyid/";

    /**
     * Get exam hsk with id
     * @param Authorization $token
     * @param string $lang
     */
    public function get(Request $request) {
        $validatorParam = Validator::make($request->route()->parameters(), [
            'hsk_id' => 'required|numeric',
        ]);
        if ($validatorParam->fails()) {
            return $this->errorResponse($validatorParam->errors(), 422);
        }

        $user = $this->getUser();

        if ($user) {
            $hskId = $request->hsk_id;

            $util = new UtilController();
        
            $exams = $util->getData($this->BASE_URL.$hskId);

            if ($exams->purchase == 1) {
                if ($user->checkPremium()) {
                    return $this->successResponse($exams);
                } else {
                    return $this->errorResponse("Your account isn't premium", 403);
                }
            } else {
                return $this->successResponse($exams);
            }
        } else {
            return $this->errorResponse("Authenticated fail", 401);
        }
    }

    public function saveHistoryUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|numeric',
            'score' => 'required|numeric',
            'part_listen' => 'required|string',
            'part_read' => 'required|string',
            'part_write' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $this->getUser();

        if ($user) {
            $examId = $request->exam_id;
            $score = $request->score;
            $part_listen = $request->part_listen;
            $part_read = $request->part_read;
            $part_write = json_decode($request->part_write);
            $write1 = [];
            $write2 = [];
            if (sizeof($part_write) > 0) {
                foreach ($part_write as $key => $item) {
                    if (isset($item->type) && ($item->type == 1)) {
                        array_push($write2, $item);
                    } else {
                        array_push($write1, $item);
                    }
                }
            }

            $history = EHistory::create([
                "exam_id" => $examId,
                "user_id" => $user->id,
                "score" => $score,
                "process" => (sizeof($write2) > 0) ? 0 : 1,
                "part_listen" => $part_listen,
                "part_read" => $part_read,
                "part_write" => json_encode($write1)
            ]);

            foreach ($write2 as $key => $item) {

                EPartWrite::create([
                    "exam_id" => $examId,
                    "user_id" => $user->id,
                    "history_id" => $history->id,
                    "question_id" => $item->question_id,
                    "order" => $item->key,
                    "content" => $item->answer,
                    "status" => 0,
                    "process" => 0,
                    "max_score" => $item->point
                ]);
            }

            $history->timestamp = $history->created_at->timestamp;

            return $this->successResponse($history);
        } else {
            return $this->errorResponse("Your aren't login", 401);
        }
    }

    public function getHistory(Request $request) {

        $validator = Validator::make($request->route()->parameters(), [
            'history_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $this->getUser();
        $historyId = $request->history_id;

        if ($user) {
            $history = EHistory::where('id', $historyId)->with('exam')->first();
            
            $part_write = EPartWrite::where('history_id', $history->id)->get();
            $write = [];
            foreach ( $part_write as $value) {
                $question = new stdClass();
                $question->key = $value->order;
                $question->answer = $value->content;
                $question->result = true;
                $question->point = $value->score ? $value->score : 0;
                $question->question_id = $value->question_id;
                $question->type = 1;

                array_push($write, $question);
            }

            $dt = json_decode($history->part_write);
            $history->timestamp = $history->created_at->timestamp;
            $history->part_write = json_encode(array_merge($dt, $write));

            return $this->successResponse($history);
        } else {
            return $this->errorResponse("Authenticated fail", 401);
        }
    }

    public function getHistoryUser(Request $request) {
        $validatorParam = Validator::make($request->route()->parameters(), [
            'exam_id' => 'required|numeric',
        ]);
        if ($validatorParam->fails()) {
            return $this->errorResponse($validatorParam->errors(), 422);
        }
        $user = $this->getUser();
        $examId = $request->exam_id;

        if ($user) {
            $history = EHistory::where('user_id', $user->id)
                                ->where('exam_id', $examId)
                                ->orderBy('created_at', 'DESC')
                                ->get();
            
            foreach ($history as $key => $item) {
                $part_write = EPartWrite::where('history_id', $item->id)->get();
                $write = [];
                foreach ( $part_write as $value) {
                    $question = new stdClass();
                    $question->key = $value->order;
                    $question->answer = $value->content;
                    $question->result = true;
                    $question->point = $value->score ? $value->score : 0;
                    $question->question_id = $value->question_id;
                    $question->type = 1;

                    array_push($write, $question);
                }

                $dt = json_decode($item->part_write);
                $history[$key]->timestamp = $item->created_at->timestamp;
                $history[$key]->part_write = json_encode(array_merge($dt, $write));

            }
            return $this->successResponse($history);
        } else {
            return $this->errorResponse("Your aren't login", 401);
        }
    }

    public function getHistoryPartWrite(Request $request) {
        $examId = $request->exam_id;
        $question_id = $request->question_id;
        $limit = $request->limit ? $request->limit : 20;

        if ($question_id) {
            $history = EPartWrite::where('exam_id', $examId)
                                ->where('question_id', $question_id)
                                ->groupBy('user_id')
                                ->with(['user' => function ($c) {
                                    $c->select('username', 'image', 'id', 'is_premium', 'premium_expired');
                                }])
                                ->orderBy('score', 'DESC')
                                ->paginate($limit);
            return $this->successResponse($history->getCollection());

        } else {
            return $this->errorResponse("Data invalid", 403);
        }
    }
}
