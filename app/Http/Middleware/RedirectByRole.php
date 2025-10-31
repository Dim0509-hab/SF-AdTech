<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class RedirectByRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        Log::info('Redirecting user with role: ' . $user->role);

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'advertiser':
                return redirect()->route('advertiser.index');
            case 'webmaster':
                return redirect()->route('webmaster.index');
            default:
                Log::error('Unknown role: ' . $user->role);
                abort(403, 'Неизвестная роль');
        }

        return $next($request);
    }
}
