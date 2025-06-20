<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function redirectToShopify(Request $request)
    {
        $shop = $request->query('shop');
        $clientId = config('shopify.api_key');

        //\Log::info('SHOPIFY_API_KEY=', [ 'key' => env('SHOPIFY_API_KEY'), 'cfg' => config('shopify.api_key') ]);

        $scopes = implode(',', [
            'read_orders',
            'write_orders',
            'read_customers',
            'write_customers',
            'write_app_proxy'
        ]);

        $redirectUri = urlencode(route('shopify.callback'));

        if (empty($shop) || empty($clientId)) {
            abort(400, 'Missing shop or API key; check logs');
        }

        $installUrl = "https://{$shop}/admin/oauth/authorize"
                    ."?client_id=" .$clientId
                    ."&scope={$scopes}"
                    ."&redirect_uri={$redirectUri}";

        return redirect($installUrl);
    }

    public function handleShopifyCallback(Request $request)
    {
        $shop = $request->query('shop');
        $code = $request->query('code');

        $client = new Client();
        $clientId = config('shopify.api_key');
        $clientSecret = config('shopify.api_secret');
        $response = $client->post("https://{$shop}/admin/oauth/access_token", [
            'form_params' => [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'code'          => $code,
            ]
        ]);

        $body = json_decode((string)$response->getBody(), true);
        // $body['access_token'] is your shop’s token. Persist it for later use.
        // e.g. in database or config file.

        // Once saved, redirect back to your app’s “home” or proxy endpoint
        return redirect("/apps/vcard-app?installed=1");
    }
}
