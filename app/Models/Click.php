<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = ['subscription_id', 'ip', 'user_agent', 'created_at'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}

