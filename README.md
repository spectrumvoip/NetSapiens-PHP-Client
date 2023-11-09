
# Installation

```
composer add spectrumvoip/netsapiensclient
```

You may have to add the following to your composer.json

```
    "minimum-stability": "dev",
    "prefer-stable": true,
```

# How to use

```
require 'vendor/autoload.php';

$nsclient = new \spectrumvoip\NetSapiensClient\NetSapiensClient('localhost');

$nsclient->login($nsclientid, $nsclientsecret, $nsusername, $nspassword);

# Calling the API to count the number of domains
$params = array();
$params['format'] = 'json';
$params['object'] = 'domain';
$params['action'] = 'count';
$params['domain'] = '*';
try {
    $response = $nsclient->ns_api_get($params);
} catch (Exception $e) {
    echo "Could not retrieve count of domains";
}
```
