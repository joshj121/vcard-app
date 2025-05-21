<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyShopifyProxy
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $next($request);
        }

        $hmacHeader = $request->header('X-Shopify-Hmac-Sha256');
        $data = $request->isMethod('POST')
        ? $request->getContent()
        : $request->getQueryString();

        $calculated = base64_encode(
        hash_hmac('sha256', $data, config('shopify.secret'), true)
        );

        if (! hash_equals($hmacHeader, $calculated)) {
            abort(403, 'Invalid Shopify signature');
        }

        return $next($request);
    }
}
