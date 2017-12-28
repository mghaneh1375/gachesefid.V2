<?php

namespace App\Http\Middleware;

use App\models\RedundantInfo1;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class QuizAccess
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
        if(RedundantInfo1::whereUId(Auth::user()->id)->count() == 0)
            return Redirect::to(route('userInfo2', ['selectedPart' => 'additional1']));
        return $next($request);
    }
}
