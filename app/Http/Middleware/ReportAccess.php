<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ReportAccess
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
        include_once __DIR__ . '/../Controllers/Common.php';

        $level = Auth::user()->level;

        if($level == getValueInfo('adminLevel') || $level == getValueInfo('superAdminLevel') ||
            $level == getValueInfo('schoolLevel') || $level == getValueInfo("namayandeLevel") || 
            $level == getValueInfo('adviserLevel'))
            return $next($request);

        return Redirect::to(route('profile'));
    }
}
