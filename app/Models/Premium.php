<?php

namespace App\Models;

use App\Models\Admins\Admin;
use Illuminate\Database\Eloquent\Model;

class Premium extends Model
{
    protected $table = 'premiums';

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
