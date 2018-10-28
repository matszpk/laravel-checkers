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
        $user = Auth::user();
        View::share('username', $user!=NULL ? $user->name : NULL);
        View::share('userrole', $user!=NULL ? $user->role : NULL);
        View::share('userid', $user!=NULL ? $user->id: NULL);
        View::share('emailVerified', $user!=NULL && $user->hasVerifiedEmail());
        return $next($request);
    }
}
