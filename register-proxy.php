<?php
require __DIR__.'/vendor/autoload.php';

// 1. Locate & parse your .env directly
$envPath = __DIR__ . '/.env';
if (! file_exists($envPath)) {
    die(".env file not found at $envPath\n");
}
$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

// 2. Pull out the values
$domain = $env['SHOPIFY_STORE_DOMAIN']  ?? null;
$token  = $env['SHOPIFY_ACCESS_TOKEN'] ?? null;
$apiVer = $env['SHOPIFY_API_VERSION']   ?? null;
$secret = $env['SHOPIFY_API_SECRET']    ?? null;

// 3. Quick debug (remove these lines once it works)
// echo "Domain: "   . var_export($domain, true) . PHP_EOL;
// echo "Token: "    . var_export($token,  true) . PHP_EOL;
// echo "APIVer: "   . var_export($apiVer, true) . PHP_EOL;
// echo "Secret: "   . var_export($secret, true) . PHP_EOL;

// 4. Bail if anything’s missing
if (! $domain || ! $token || ! $apiVer || ! $secret) {
    die("One or more Shopify vars are missing from .env\n");
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
