<?php

namespace App\Models;

use App\Models\Admins\Admin;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    public function user(){
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
