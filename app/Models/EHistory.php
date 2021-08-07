<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EHistory extends Model
{
    protected $table = 'e_histories';
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    // public function exam(){
    //     return $this->belongsTo(Exam::class, 'exam_id', 'id');
    // }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
