<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{

public function handle($request, $next, $role)
{
    if (! $request->user() || ! $request->user()->hasRole($role)) {
        abort(403);
    }
    return $next($request);
}
}
