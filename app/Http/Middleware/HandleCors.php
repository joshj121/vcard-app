<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins  = 'https://apollomfgshop.com';
        $allowedMethods  = 'GET, POST, OPTIONS';
        $allowedHeaders  = 'Accept, Authorization, Content-Type, X-Requested-With, X-Shopify-Hmac-Sha256';

        // If this is a preflight request, return immediately with CORS headers
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $allowedOrigins)
                ->header('Access-Control-Allow-Methods', $allowedMethods)
                ->header('Access-Control-Allow-Headers', $allowedHeaders)
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // Otherwise run the request and then add headers to the response
        /** @var Response $response */
        $response = $next($request);

        return $response
            ->header('Access-Control-Allow-Origin', $allowedOrigins)
            ->header('Access-Control-Allow-Methods', $allowedMethods)
            ->header('Access-Control-Allow-Headers', $allowedHeaders)
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
