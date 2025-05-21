<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If this is a preflight request, just return 204
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', 'https://apollomfgshop.com')
                ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Shopify-Hmac-Sha256')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // Otherwise, handle the request and add CORS headers to the response
        /** @var Response $response */
        $response = $next($request);

        return $response
            ->header('Access-Control-Allow-Origin', 'https://apollomfgshop.com')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Shopify-Hmac-Sha256')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
