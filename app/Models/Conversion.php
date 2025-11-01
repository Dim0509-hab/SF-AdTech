<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversion extends Model
{
    use HasFactory;

    // 1. Укажите имя таблицы (если не следует правилам Laravel)
    protected $table = 'conversions';

    // 2. Укажите первичный ключ (если не `id`)
    protected $primaryKey = 'id';

    // 3. Разрешите массовое заполнение для нужных полей
    protected $fillable = [
        'user_id',
        'offer_id',
        'status',
        'revenue',
        'conversion_date',
        'ip_address',
        'user_agent',
        'referrer',
        // добавьте другие поля по необходимости
    ];

    // 4. Укажите поля, которые должны быть датами
    protected $dates = [
        'conversion_date',
        'created_at',
        'updated_at',
    ];

    // 5. Автоматически приводите поля к нужным типам
    protected $casts = [
        'revenue' => 'decimal:2',
        'user_id' => 'integer',
        'offer_id' => 'integer',
        'conversion_date' => 'datetime',
    ];

    // 6. Отключите временные метки, если не нужны (по умолчанию — включены)
    // public $timestamps = false;

    // 7. Пример связи с моделью User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 8. Пример связи с моделью Offer
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    // 9. Пример scope для активных конверсий
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    // 10. Пример мутатора для поля status (опционально)
    public function setStatusAttribute($value)
    {
        $validStatuses = ['pending', 'approved', 'rejected', 'draft'];
        if (!in_array($value, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid status: ' . $value);
        }
        $this->attributes['status'] = $value;
    }
}
