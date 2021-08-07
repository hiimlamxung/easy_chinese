<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translate extends Model
{
    protected $table = 'news_trans';
    protected $guraded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function author() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function react() {
        return $this->hasMany(TransLike::class, 'trans_id', 'id');
    }

}
