<?php
// app/Http/Controllers/VCardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class VCardController extends Controller
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => "https://".config('shopify.domain')."/admin/api/".config('shopify.api_version')."/",
            'headers'  => [
                'X-Shopify-Access-Token' => config('shopify.access_token'),
                'Content-Type'           => 'application/json',
            ],
        ]);
    }

    public function handle(Request $request)
    {
        return $request->isMethod('GET')
            ? $this->show($request)
            : $this->store($request);
    }

    protected function show(Request $request)
    {
        $orderId = $request->query('order');
        if (! $orderId) {
            return response()->json(['error' => 'Missing order parameter'], 400);
        }

        $resp = $this->client->get("orders/{$orderId}/metafields.json", [
            'query' => ['namespace' => 'custom'],
        ]);
        $body = json_decode($resp->getBody(), true)['metafields'];

        $out = [];
        foreach ($body as $mf) {
            $out[$mf['key']] = $mf['value'];
        }

        return response()->json([
            'vcard_data'     => json_decode($out['vcard_data']     ?? '{}', true),
            'vcard_summary'  => $out['vcard_summary']  ?? null,
            'vcard_notes'    => $out['vcard_notes']    ?? null,
            'vcard_finalized'=> filter_var($out['vcard_finalized'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            'vcard_updated_at'=> $out['vcard_updated_at'] ?? null,
        ]);
    }

    protected function store(Request $request)
    {
        $orderId = $request->query('order');
        if (! $orderId) {
            return response()->json(['error' => 'Missing order parameter'], 400);
        }

        $payload = $request->json()->all();
        $nowISO  = now()->toIso8601String();

        $fields = [
          'vcard_data'      => ['value' => json_encode($payload['vcard_data']),   'type' => 'json'],
          'vcard_summary'   => ['value' => $payload['vcard_summary'],             'type' => 'multi_line_text_field'],
          'vcard_notes'     => ['value' => $payload['vcard_notes'],               'type' => 'multi_line_text_field'],
          'vcard_finalized' => ['value' => $payload['vcard_finalized']?'true':'false','type'=>'boolean'],
          'vcard_updated_at'=> ['value' => $nowISO,                               'type' => 'date_time'],
        ];

        // fetch existing
        $resp = $this->client->get("orders/{$orderId}/metafields.json", [
            'query' => ['namespace' => 'custom'],
        ]);
        $existing = [];
        foreach (json_decode($resp->getBody(), true)['metafields'] as $mf) {
            $existing[$mf['key']] = $mf['id'];
        }

        // upsert each field
        foreach ($fields as $key => $info) {
            if (isset($existing[$key])) {
                $this->client->put("metafields/{$existing[$key]}.json", [
                    'json' => ['metafield' => [
                        'id'    => $existing[$key],
                        'value' => $info['value'],
                        'type'  => $info['type'],
                    ]],
                ]);
            } else {
                $this->client->post("orders/{$orderId}/metafields.json", [
                    'json' => ['metafield' => [
                        'namespace' => 'custom',
                        'key'       => $key,
                        'value'     => $info['value'],
                        'type'      => $info['type'],
                    ]],
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
