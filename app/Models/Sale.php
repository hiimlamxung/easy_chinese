<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sale_off';
    protected $guraded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
