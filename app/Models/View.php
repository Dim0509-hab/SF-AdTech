<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $offer_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUserAgent($value)
 * @mixin \Eloquent
 */
class View extends Model
{
    protected $fillable = ['offer_id', /* другие поля */];

    // Если нужна связь обратно к Offer
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
