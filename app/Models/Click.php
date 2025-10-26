<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $table = 'clicks'; // имя таблицы в БД

    protected $fillable = [
        'offer_id',
        'webmaster_id',
        'cost',       // стоимость клика
        'click_id',  // если есть отдельный идентификатор клика
        // другие поля
    ];

    // Связь с оффером
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
