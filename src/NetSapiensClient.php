<?php
namespace spectrumvoip\NetSapiensClient;

class NetSapiensClient
{
    const TOKEN_EXPIRY_BUFFER = 'PT5M';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var string
     */
    protected $tokenType;

    /**
     * @var string
     */
    protected $refreshToken;


    /**
     * @var \DateTime
     */
    protected $tokenExpiry;

    /**
     * @var string
     */
    protected $legacy;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $hostname;

    /**
     * @param string $hostname
     */
    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param string $clientId
     * @param string $secret
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    public function login($clientId, $secret, $username, $password)
    {
        $parameters = [
            'grant_type'    => 'password',
            'client_id'     => $clientId,
            'client_secret' => $secret,
            'username'      => $username,
            'password'      => $password,
        ];

        $header = array();
        $url = $this->getApiUrl('/ns-api/oauth2/token');
        $response = $this->curl_post($url, $header, $parameters);
        $data = json_decode($response);

        if (!property_exists($data, 'access_token')) {
            throw new \Exception('NetSapiens client failed to sign in.');
        }

        $this->accessToken = $data->access_token;
        $this->refreshToken = $data->refresh_token;
        $this->scope = $data->scope;
        $this->tokenType = $data->token_type;
        $this->refreshToken = $data->refresh_token;
        $this->apiVersion = $data->apiversion;

        $data = $this->introspect_token();
        date_default_timezone_set('UTC');
        $expiry_datetime = new \DateTime($data['expires']);
        $timezone = new \DateTimeZone('UTC');
        $expiry_datetime->setTimezone($timezone);
        $this->tokenExpiry = $expiry_datetime;
    }

    private function getApiUrl($path)
    {
        if ($this->hostname === "localhost") {
            return 'localhost' . $path;
        } else {
            return 'https://' . $this->hostname . $path;
        }
    }


    /**
     * @return string
     */
    public function hostname()
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function scope()
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function apiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @return string
     */
    public function accessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public function check_expiry()
    {
        $now = new \DateTime('now');
        $now->setTimezone(new \DateTimeZone('UTC'));

        # Add a buffer period
        # Note that this is performed in place.
        $now->add(new \DateInterval(self::TOKEN_EXPIRY_BUFFER));
        return $now < $this->tokenExpiry;
    }

    /**
     * @param array $params
     *
     * @return Object
     */
    public function ns_api_get(array $params)
    {
        if (!$this->check_expiry()) {
            $this->refresh_token();
        }
    
        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        $url = $this->getApiUrl('/ns-api/');
        $response = $this->curl_get($url.'?'.http_build_query($params), $header);
        return json_decode($response, true);
    }

    /**
     * @param array $params
     *
     * @return Object
     */
    public function ns_api_post(array $params)
    {
        if (!$this->check_expiry()) {
            $this->refresh_token();
        }
    
        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        $url = $this->getApiUrl('/ns-api/');
        $response = $this->curl_post($url, $header, $params);
        return json_decode($response, true);
    }

    /**
     * @return Object
     */
    public function introspect_token()
    {
        $params = [];
        $params['format'] = 'json';

        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        $url = $this->getApiUrl('/ns-api/oauth2/read');
        $response = $this->curl_get($url.'?'.http_build_query($params), $header);
        return json_decode($response, true);
    }

    /**
     * @return Object
     */
    public function refresh_token()
    {
        $params = [];
        $params['grant_type'] = 'refresh_token';
        $params['refresh_token'] = $this->refreshToken;
        $params['client_id'] = $this->clientId;
        $params['client_secret'] = $this->clientSecret;

        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        $url = $this->getApiUrl('/ns-api/oauth2/token');
        $response = $this->curl_get($url.'?'.http_build_query($params), $header);
        return json_decode($response, true);
    }

    /**
     * @param string $url
     * @param array $header
     *
     * @return string
     */
    public function curl_get($url, array $header)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            throw new \Exception($err);
        } else {
            return $response;
        }
    }

    /**
     * @param string $url
     * @param array $header
     * @param array $vars
     *
     * @return string
     */
    public function curl_post($url, array $header, array $vars)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            throw new \Exception($err);
        } else {
            return $response;
        }
    }
}
