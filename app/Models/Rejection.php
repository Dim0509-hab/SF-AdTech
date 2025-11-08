<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $webmaster_id
 * @property int $offer_id
 * @property string $reason
 * @property \Illuminate\Support\Carbon $rejected_at
 * @property-read \App\Models\Offer $offer
 * @property-read \App\Models\User $webmaster
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rejection whereWebmasterId($value)
 * @mixin \Eloquent
 */
class Rejection extends Model
{
    protected $fillable = [
        'webmaster_id',
        'offer_id',
        'link_hash',
        'reason',
        'context',
    ];

    protected $casts = [
        'context' => 'json',
    ];

    public function webmaster()
    {
        return $this->belongsTo(\App\Models\User::class, 'webmaster_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
