<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $offer_id
 * @property string $date
 * @property string $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AdSpend> $adSpends
 * @property-read int|null $ad_spends_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdSpend whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdSpend extends Model
{

    public function adSpends()
{
    return $this->hasMany(AdSpend::class);
}

    //
}
