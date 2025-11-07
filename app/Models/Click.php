<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class Click extends Model
{
    protected $fillable = [
    'offer_id',
    'webmaster_id',
    'link_hash',
    'click_id',
    'cost',
    'user_agent',
    'count',
];


    protected $dates = ['created_at'];
    protected $table = 'clicks'; // Если таблица не 'click'




    public function webmaster()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
