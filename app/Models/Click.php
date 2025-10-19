<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Click extends Model {
    protected $fillable = ['offer_id','webmaster_id','click_token','referer','ip','user_agent','cost','redirected','clicked_at','redirected_at'];
    public function offer(){ return $this->belongsTo(\App\Models\Offer::class); }
    public function webmaster(){ return $this->belongsTo(\App\Models\User::class,'webmaster_id'); }
}
