<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && is_null(Auth::user()->password_changed_at)) {
            return redirect()->route('account.index')->with('warning', 'Change your password before continuing!');
        }

        return $next($request);
    }
}