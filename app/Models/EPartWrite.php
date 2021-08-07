<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class EPartWrite extends Model
{
    protected $table = 'e_part_writes';
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    // public function exam() {
    //     return $this->hasMany(Exam::class, 'exam_id', 'id');
    // }

    // public function question() {
    //     return $this->belongsTo(EQuestion::class, 'question_id', 'id');
    // }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
