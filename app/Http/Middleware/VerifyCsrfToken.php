<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Closure;

class VerifyCsrfToken extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, \Closure $next)
    {
        // **TEMPORARILY SKIP ALL CSRF CHECKS**
        return $next($request);
    }

    // public function handle(Request $request, Closure $next)
    // {
    //     // 1️⃣ If this is your App Proxy POST (or GET) path, skip CSRF entirely:
    //     if ($request->is('apps/vcard-app')) {
    //         return $next($request);
    //     }

    //     // 2️⃣ Otherwise, let Laravel do its normal CSRF check:
    //     return parent::handle($request, $next);
    // }
}
