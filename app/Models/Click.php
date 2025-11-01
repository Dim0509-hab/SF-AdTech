<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class Click extends Model
{
    protected $fillable = [
        'subscription_id',
        'ip',
        'user_agent',
        'created_at',
        'webmaster_id',
        'offer_id',
        'link_hash',
        'referrer'
    ];

    protected $dates = ['created_at'];
    protected $table = 'clicks'; // Если таблица не 'click'



    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
    public function webmaster()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
