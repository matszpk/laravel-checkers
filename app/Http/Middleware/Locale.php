<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Closure;

class Locale
{
    const LOCALES = [ 'en', 'pl' ];

    public function handle(Request $request, Closure $next)
    {
        $locale = NULL;
        // get from request
        $locale = $request->getPreferredLanguage(self::LOCALES);
        App::setLocale($locale);
        return $next($request);
    }
}
