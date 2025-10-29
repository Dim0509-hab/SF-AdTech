<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSpend extends Model
{

    public function adSpends()
{
    return $this->hasMany(AdSpend::class);
}

    //
}
