<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $table = 'conversions'; // имя таблицы в БД

    protected $fillable = [
        'offer_id',
        'webmaster_id',
        'amount',     // сумма конверсии

    ];

    // Связь с оффером
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
