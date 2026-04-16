<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // OPTIMIZED: Redirect based on user type, not always HOME
                $user = Auth::guard($guard)->user();
                
                if (in_array($user->type, ['company', 'super admin', 'client'])) {
                    return redirect(RouteServiceProvider::HOME);
                } else {
                    return redirect(RouteServiceProvider::EMPHOME);
                }
            }
        }

        return $next($request);
    }
}
