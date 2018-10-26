<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Closure;

class CommonViewData
{
    public function handle(Request $request, Closure $next)
    {
        View::share('username', Auth::user());
        return $next($request);
    }
}
