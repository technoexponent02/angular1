<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Session;

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
            return redirect('/');
        }

        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */

    /*public function terminate($request, $response)
    {
        $cookieName = Auth::getRecallerName();
        // Store the session data...
        if (Session::has('cookie_expiration')  && Auth::check() && isset($_COOKIE[$cookieName])){
            // get the (current/new) cookie values
            $cookieValue = $_COOKIE[$cookieName];//Cookie::get($cookieName);
            $expiration  = Session::get('cookie_expiration');

            $arr[] = "Cookie Name = ".$cookieName;
            $arr[] = "Cookie value = ".$cookieValue;
            $arr[] = "Session = ".$expiration;

            $handle = fopen(public_path()."/cookie.txt", "w");
            fwrite($handle, implode("\n", $arr));
            fclose($handle);

            // forget the session var
            Session::forget('cookie_expiration');

            // change the expiration time
            //$cookie = Cookie::make($cookieName, $cookieValue, $expiration);

            return $response->withCookie(cookie("$cookieName", "$cookieValue", $expiration));

        }
    }*/


}
