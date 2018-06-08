<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class Authenticate
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
		/*$user = User::find(10); 
		if(!Auth::check()){
			Auth::guard($guard)->login($user);
		}*/
		
        if (Auth::guard($guard)->guest()) {
			
            if ($request->ajax() || $request->wantsJson()) {  
			
				
                return response('Unauthorized.', 401);
            }

            //return redirect()->guest('login');//(8-11-17)
            return redirect()->guest('explore');//(8-11-17)
        }

        $response = $next($request);

        $cookieName = Auth::getRecallerName();
        $cookieValue = Auth::user()->remember_token;
        $expiration = 42000;

        return $response->withCookie($cookieName, $cookieValue, $expiration);
    }
}
