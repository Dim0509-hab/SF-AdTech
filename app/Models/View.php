<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['offer_id', /* другие поля */];

    // Если нужна связь обратно к Offer
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
