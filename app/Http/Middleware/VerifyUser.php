<?php

namespace App\Http\Middleware;

use Closure;

class VerifyUser
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
        $isVerified = auth()->user()->cats;
        
        if ( $isVerified ) {
            return $next($request);
        }

        return redirect('/verify');
    }
}
