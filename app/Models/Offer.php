<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

     protected $fillable = [
        'advertiser_id',
        'name',
        'price',
        'target_url',
        'themes', // изменено с 'topics' на 'themes'
        'active'
    ];

    protected $casts = [
        'themes' => 'array',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    // 💼 Связь с рекламодателем
    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    // 🌐 Связь с веб-мастерами
    public function webmasters()
    {
        return $this->belongsToMany(User::class, 'offer_webmaster', 'offer_id', 'webmaster_id')
                    ->withTimestamps()
                    ->withPivot('agreed_price');
    }

    // 📈 Переходы по офферу
    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
