<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Models\RoleMiddleware;

class Authenticate extends Middleware
{
    /**
     * Куда перенаправлять неавторизованных пользователей.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
