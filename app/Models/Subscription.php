<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Offer;
use App\Models\Click;

class Subscription extends Model
{
    protected $fillable = ['webmaster_id', 'offer_id', 'cost_per_click', 'system_link'];

    public function webmaster()
    {
        return $this->belongsTo(User::class, 'webmaster_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}


