<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    /**
     * status = 0: new
     * status = 1: wating
     * status = 2: actived
     */
    protected $table = 'codes';
    protected $guarded = ['id'];

    public function premium(){
        return $this->belongsTo(Premium::class, 'code', 'code');
    }

    public function actived(){
        $this->status = 2;
        return $this->save();
    }
}
