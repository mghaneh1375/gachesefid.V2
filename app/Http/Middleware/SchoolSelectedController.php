<?php

namespace App\Http\Middleware;

use App\models\SchoolStudent;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SchoolSelectedController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if(SchoolStudent::whereUId(Auth::user()->id)->count() > 0)
            return $next($request);

        return Redirect::route('schoolsList');
    }
}
