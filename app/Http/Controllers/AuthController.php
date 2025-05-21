<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function redirectToShopify(Request $request)
    {
        $shop = $request->query('shop');
        $scopes = 'read_orders,write_orders,read_customers,write_customers,read_metafields,write_metafields';
        $redirectUri = urlencode(route('shopify.callback'));
        $installUrl = "https://{$shop}/admin/oauth/authorize"
                    ."?client_id=" .config('shopify.client_id')
                    ."&scope={$scopes}"
                    ."&redirect_uri={$redirectUri}";

        return redirect($installUrl);
    }

    public function handleShopifyCallback(Request $request)
    {
        $shop = $request->query('shop');
        $code = $request->query('code');

        $client = new Client();
        $response = $client->post("https://{$shop}/admin/oauth/access_token", [
            'form_params' => [
                'client_id'     => config('shopify.client_id'),
                'client_secret' => config('shopify.client_secret'),
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
