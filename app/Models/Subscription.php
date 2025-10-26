<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'offer_id',
        'webmaster_id',
        // другие поля, которые можно заполнять
    ];

    // Связь с оффером
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    // Связь с веб‑мастером (пользователем)
    public function webmaster()
    {
        return $this->belongsTo(User::class, 'webmaster_id');
    }
}
