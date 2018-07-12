<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PhoneController
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
        if(Auth::user()->phoneNum == "" || (Auth::user()->level == getValueInfo('studentLevel') && Auth::user()->NID ==
                null))
            return Redirect::to('userInfo');

        return $next($request);
    }
}
