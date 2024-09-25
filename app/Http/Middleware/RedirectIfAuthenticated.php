<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $role = Auth::User()->role;

            switch ($role) {
                case '1':
                    return redirect('/Dashboard');
                break;
    
                case '2':
                    return redirect('/Dashboard');
                break;
    
                case '3':
                    return redirect('/Dashboard');
                break;

                case '4':
                    return redirect('/Dashboard');
                break;
                case '5':
                    return redirect('/Dashboard');
                break;
    
                case '6':
                    return redirect('/Dashboard');
                break;
    
                case '7':
                    return redirect('/Dashboard');
                break;

                case '8':
                    return redirect('/Dashboard');
                break;
    
                default:
                    return '/';
                break;
            }
        }

        return $next($request);
    }
}
