<?php

namespace App\Http\Middleware;

use App\Exceptions\LoginFailed;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (isset($user->token) && $user->token != request()->bearerToken()) {
            throw new LoginFailed();
        }

        return $next($request);
    }
}
