# NetSapiens PHP Client Documentation

## Maintenance Mode Notice

This repository is now in maintenance mode as the API has been upgraded to version 2. All future development efforts are focused on the v2 client, and support for v1 will be discontinued. 

- If you're using the v1 API, we recommend migrating to the [v2 client](https://github.com/spectrumvoip/netsapiens-openapi-php).

Thank you for your understanding.

## About

This PHP client is designed to interact with the NetSapiens V1 API. It provides a convenient way to authenticate and make API calls to NetSapiens services.

## Installation

To install the NetSapiens PHP Client, use Composer:

```bash
composer require spectrumvoip/netsapiensclient
```

## Basic Usage

### Initialization

First, include the Composer autoloader in your PHP script:

```php
require 'vendor/autoload.php';
```

Then, create an instance of the NetSapiensClient:

```php
$nsclient = new \spectrumvoip\NetSapiensClient\NetSapiensClient('your-hostname.com');
```

### Authentication

To authenticate with the NetSapiens API:

```php
$nsclient->login($clientId, $clientSecret, $username, $password);
```

Replace `$clientId`, `$clientSecret`, `$username`, and `$password` with your actual credentials.

### Making API Calls

#### GET Request Example

Here's an example of how to count the number of domains:

```php
$params = [
    'format' => 'json',
    'object' => 'domain',
    'action' => 'count',
    'domain' => '*'
];

try {
    $response = $nsclient->ns_api_get($params);
    echo "Number of domains: " . $response['total'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

#### POST Request Example

To make a POST request:

```php
$params = [
    'format' => 'json',
    'object' => 'user',
    'action' => 'create',
    // Add other necessary parameters
];

try {
    $response = $nsclient->ns_api_post($params);
    echo "User created successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Advanced Features

### Token Refresh

The client automatically refreshes the access token when it's close to expiration. You don't need to manually refresh the token in most cases.

### Masquerading

To masquerade as another user:

```php
$masqueradeResponse = $nsclient->masquerade_token($uid, $accessToken);
```

Replace `$uid` with the user ID you want to masquerade as, and `$accessToken` with a valid access token.

## Error Handling

The client throws exceptions for API errors and connection issues. Always wrap your API calls in try-catch blocks to handle potential errors gracefully.

## API Reference

For a complete list of available API methods and their parameters, please refer to the NetSapiens API documentation.

## Contributing

If you find any issues or have suggestions for improvements, please open an issue or submit a pull request on the GitHub repository.

## License

This project is licensed under the MIT License.

