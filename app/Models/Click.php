<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

/**
 * @property int $id
 * @property int $offer_id
 * @property string|null $link_hash
 * @property int|null $webmaster_id
 * @property string $cost
 * @property string|null $click_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $count
 * @property string|null $user_agent
 * @property int|null $subscription_id
 * @property-read \App\Models\Offer $offer
 * @property-read \App\Models\User|null $webmaster
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereClickId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereLinkHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Click whereWebmasterId($value)
 * @mixin \Eloquent
 */
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
     'click_token',
       'ip',
    'clicked_at',
     'redirected',
      'redirected_at',
       'final_url',
    'redirect_attempts',
     'redirect_error'
];



    protected $dates = ['created_at'];
    protected $table = 'clicks';


   public function webmaster()
    {
        return $this->belongsTo(\App\Models\User::class, 'webmaster_id');
    }

    public function offer()
    {
        return $this->belongsTo(\App\Models\Offer::class, 'offer_id');
    }
}
