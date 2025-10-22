<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property bool $active
 */


class User extends Authenticatable {
    use HasFactory;
    protected $fillable = ['name','email','password','role','active'];
    protected $hidden = ['password','remember_token'];

    public function offers(){ return $this->hasMany(\App\Models\Offer::class,'advertiser_id'); }
    public function subscriptions(){ return $this->belongsToMany(\App\Models\Offer::class,'offer_webmaster','webmaster_id','offer_id')->withTimestamps()->withPivot('agreed_price'); }
    public function clicks(){ return $this->hasMany(\App\Models\Click::class,'webmaster_id'); }
}
