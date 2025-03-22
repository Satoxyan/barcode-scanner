<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AutoLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cari user dengan email yang benar
        $user = User::where('email', 'user@user.com')->first();

        if ($user && !Auth::check()) {
            Auth::login($user);
        }

        return $next($request);
    }
}
