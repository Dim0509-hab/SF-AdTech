<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    protected $fillable = ['webmaster_id', 'offer_id', 'cost_per_click', 'system_link'];

    public function webmaster()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}

