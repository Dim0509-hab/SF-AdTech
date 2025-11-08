<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

/**
 * @property int $id
 * @property string $link_hash
 * @property int $advertiser_id
 * @property string $name
 * @property string $status
 * @property numeric $price
 * @property string $target_url
 * @property array<array-key, mixed>|null $themes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $active
 * @property string|null $revenue_per_click Доход с одного клика по офферу
 * @property-read \App\Models\User $advertiser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Click> $clicks
 * @property-read int|null $clicks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversion> $conversions
 * @property-read int|null $conversions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $subscribers
 * @property-read int|null $subscribers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $webmasters
 * @property-read int|null $webmasters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereAdvertiserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereLinkHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereRevenuePerClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereTargetUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereThemes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Offer extends Model
{
    use HasFactory;

     protected $fillable = [
        'advertiser_id',
        'name',
        'price',
        'description',
        'target_url',
        'themes',
        'status',
        'revenue_per_click',
        'link_hash',
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
        return $this->belongsToMany(
            \App\Models\User::class,
            'offer_webmaster',
            'offer_id',
            'webmaster_id'
        )->withPivot('cost_per_click', 'agreed_price', 'status');
    }

    public function subscribers()
        {
            return $this->belongsToMany(
                \App\Models\User::class,
                'offer_webmaster',
                'offer_id',
                'webmaster_id'
            )->withPivot('cost_per_click', 'status', 'created_at');
        }

    // 📈 Переходы по офферу
        public function clicks()
    {
        return $this->hasMany(\App\Models\Click::class, 'offer_id');
    }
    /**
     * Получить статистику для оффера
     */
    public function getStats()
    {
        // Пример реализации (адаптируйте под свою логику)
        return [
            'views' => $this->views()->count(),
            'clicks' => $this->clicks()->count(),
            'conversions' => $this->conversions()->count(),
            'revenue' => $this->conversions()->sum('amount'),
        ];
    }
    public function views()
    {
       return $this->hasMany(\App\Models\View::class, 'offer_id', 'id');
    }
    public function conversions()
    {
    return $this->hasMany(\App\Models\Conversion::class, 'offer_id', 'id');
    }



}
