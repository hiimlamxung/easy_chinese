<?php

namespace App\Models;

use App\Models\Admins\Admin;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    const CREATED = 0;
    const PUBLISH = 1;
    const DELETED = -1;
    const SUCCESS = 2;

    protected $table = 'news';
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function admins(){
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function comments(){
        return $this->hasMany(Comment::class, 'news_id', 'id');
    }

    public function success(){
        $this->status = self::SUCCESS;
    }
}
