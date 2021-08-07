<?php

namespace App\Models\Admins;

use App\Models\News;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $guard = 'admin';

    protected $guarded = [];
    public function news(){
        return $this->hasMany(News::class,'user_id', 'id');
    }

    public function actionNews(){
        return $this->hasMany(News::class, 'user_post', 'id');
    }
}
