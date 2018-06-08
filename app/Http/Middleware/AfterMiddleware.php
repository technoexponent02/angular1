<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class AfterMiddleware
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
        $response = $next($request);

        //Do Stuff
        $cookieName = Auth::getRecallerName();
        $user       = Auth::user();
        /*dump(cookie()->get($cookieName));
        dd();*/

        if (Session::has('cookie_expiration')  && Auth::check()) {
            // get the (current/new) cookie values
            $cookieValue = $user->remember_token;//Cookie::get($cookieName);
            $expiration = Session::get('cookie_expiration');

            $arr[] = "Cookie Name = " . $cookieName;
            $arr[] = "Cookie value = " . $cookieValue;
            $arr[] = "Session = " . $expiration;

            $handle = fopen(public_path() . "/cookie.txt", "w");
            fwrite($handle, implode("\n", $arr));
            fclose($handle);

            // forget the session var
            Session::forget('cookie_expiration');
            $response->withCookie($cookieName, $cookieValue, $expiration);
        }

        return $response;
    }
}
