<?php
require __DIR__.'/vendor/autoload.php';

// ─── Load your .env file ────────────────────────────────────────────────
Dotenv\Dotenv::createImmutable(__DIR__)->load();

// ─── Now read them safely ────────────────────────────────────────────────
$domain = getenv('SHOPIFY_STORE_DOMAIN');
$token  = getenv('SHOPIFY_ACCESS_TOKEN');
$apiVer = getenv('SHOPIFY_API_VERSION');

if (! $domain || ! $token || ! $apiVer) {
    die("Missing SHOPIFY_STORE_DOMAIN, SHOPIFY_ACCESS_TOKEN, or SHOPIFY_API_VERSION in environment\n");
}

// ─── Build the GraphQL client ────────────────────────────────────────────
use GuzzleHttp\Client;

$client = new Client([
  'base_uri' => "https://{$domain}/admin/api/{$apiVer}/graphql.json",
  'headers'  => [
    'X-Shopify-Access-Token' => $token,
    'Content-Type'           => 'application/json',
  ],
]);

// ─── The rest of your mutation code unchanged ─────────────────────────────
$mutation = <<<'GQL'
mutation appProxyCreate($input: AppProxyInput!) {
  appProxyCreate(input: $input) {
    appProxy {
      id
      subPath
      fullSubPath
      proxyUrl
      contentType
    }
    userErrors {
      field
      message
    }
  }
}
GQL;

$variables = [
  'input' => [
    'subPathPrefix' => 'apps',
    'subPath'       => 'vcard-app',
    'proxyUrl'      => 'https://vcard.apollomfgshop.com/apps/vcard-app',
    'contentType'   => 'JSON',
  ]
];

$response = $client->post('', [
  'json' => ['query' => $mutation, 'variables' => $variables]
]);

$data = json_decode((string)$response->getBody(), true);

if (!empty($data['data']['appProxyCreate']['userErrors'])) {
  echo "Errors:\n";
  print_r($data['data']['appProxyCreate']['userErrors']);
  exit(1);
}

echo "✅ App Proxy registered!\n";
print_r($data['data']['appProxyCreate']['appProxy']);
