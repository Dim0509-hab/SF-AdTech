<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = [
        'offer_id',
        'amount',
        // другие поля вашей таблицы conversions
    ];

    // Отношение обратно к Offer (опционально)
    public function offer()
    {
        return $this->belongsTo(\App\Models\Offer::class, 'offer_id', 'id');
    }
}
