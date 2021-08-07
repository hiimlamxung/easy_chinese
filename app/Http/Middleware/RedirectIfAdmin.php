<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if(!auth()->guard($guard)->check()){
            return redirect()->route('backend.index');
        }
        $admin = auth()->guard($guard)->user();
        View::share('guard', $admin);
        return $next($request);
    }
}
