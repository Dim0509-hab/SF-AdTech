<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use App\Models\Role;

class TrimStrings extends Middleware
{
    /**
     * Атрибуты, которые не нужно обрезать.
     *
     * @var array<int, string>
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
