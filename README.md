
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
```
