<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use App\Models\Role;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI, исключённые из CSRF-проверки.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
