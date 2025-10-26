<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use App\Models\Role;

class EncryptCookies extends Middleware
{
    /**
     * Куки, которые не нужно шифровать.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
