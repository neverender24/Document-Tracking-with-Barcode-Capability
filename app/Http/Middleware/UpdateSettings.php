<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateSettings
{

    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()) {
            $isVerified = auth()->user()->cats;

            if ( $isVerified ) {
                return $next($request);
            } else {
                return redirect('/update-settings');
            }
        }
        
    }
}
