<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function redirectToShopify(Request $request)
    {
        $shop = $request->query('shop');
        $clientId = env('SHOPIFY_API_KEY');
        //$scopes = 'read_orders,write_orders,read_customers,write_customers,read_metafields,write_metafields';

        $scopes = implode(',', [
            'read_orders',
            'write_orders',
            'read_customers',
            'write_customers',
            'write_app_proxy'
        ]);

        $redirectUri = urlencode(route('shopify.callback'));
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
        $clientId = env('SHOPIFY_API_KEY');
        $clientSecret = env('SHOPIFY_API_SECRET');
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
