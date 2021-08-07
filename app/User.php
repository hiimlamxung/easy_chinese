<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * is_premium: premium forever
     * premium_expired: user buy pack 1 month, 12 month
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'provider', 'provider_id', 'apple_token', 'apple_user', 'language'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function isLogin($token){
        return User::where("remember_token", $token)->whereStatus(1)->first();
    }

    public function scopePremium($query){
        return $query->whereIsPremium(true);
    }

    public function registerUser($data){
        if ($this->checkUserUnique($data['name'], $data['email'])) {
            return false;
        } else {
            return $this->create($data);
        }
    }

    public function checkUserUnique($name, $email){
        return $this->where('name', $name)->where('email', $email)->exists();
    }

    public function findToken($token){
        return $this->where('remember_token', $token)
                    ->where('status', 1)
                    ->first();
    }

    public function setImage($image){
        $this->image = $image;
        return $this->save();
    }

    public function isPremium(){
        return $this->is_premium;
    }

    public function checkPremium() {
        if ( $this->is_premium) {
            return true;
        } else {
            if ($this->premium_expired) {
                $time = time();
                if ($this->premium_expired > $time) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function setPremiumExpired($timestamp, $status){
        if ($status) {
            $this->premium_expired = 0;
            $this->is_premium = true;
           
        } else if ($timestamp) {
            $now = Carbon::now();
            if ($this->premium_expired && ($this->premium_expired >= $now->timestamp)) {
                $this->premium_expired += $timestamp;
            } else {
                $this->premium_expired = $timestamp;
            }
        }

        $this->save();
    }
}
