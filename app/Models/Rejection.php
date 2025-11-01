<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rejection extends Model
{
    protected $fillable = ['webmaster_id', 'offer_id', 'reason'];
    protected $casts = ['rejected_at' => 'datetime'];

    public function webmaster()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
