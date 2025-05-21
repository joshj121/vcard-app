<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Exempt your App Proxy route:
        'apps/vcard-app',
        // If you ever add subpaths:
        'apps/vcard-app/*',
    ];
}