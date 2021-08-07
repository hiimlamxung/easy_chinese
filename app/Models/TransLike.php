<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransLike extends Model
{
    protected $table = 'trans_like';
    protected $guraded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
