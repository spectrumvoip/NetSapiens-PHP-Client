<?php
namespace SpectrumVoIP;

class NetSapiensClient
{

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
    protected $token_type;

    /**
     * @var string
     */
    protected $refreshToken;

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
        if ($this->hostname === "localhost")
        {
            $url = 'localhost/ns-api/oauth2/token';
        } else {
            $url = 'https://' . $this->hostname . '/ns-api/oauth2/token/';
        }
        $response = $this->curl_post($url, $header, $parameters);

        if (!property_exists($response, 'access_token')) {
            throw new \Exception('NetSapiens client failed to sign in.');
            $this->hostname = null;
        } else {
            $this->accessToken = $data->access_token;
            $this->scope = $data->scope;
            $this->token_type = $data->token_type;
            $this->refreshToken = $data->refresh_token;
            $this->legacy = $data->legacy;
            $this->apiVersion = $data->apiversion;
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

    /**
     * @param array $params
     *
     * @return Object
     */
    public function ns_api_get(array $params) {

        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        if ($this->hostname === "localhost")
        {
            $url = 'localhost/ns-api/';
        } else {
            $url = 'https://' . $this->hostname . '/ns-api/';
        }
        $response = $this->curl_get($url.'?'.http_build_query($params), $header);
        return json_decode($response, true);
    }

    /**
     * @param array $params
     *
     * @return Object
     */
    public function ns_api_post(array $params) {

        $header = array();
        $header[] = "Authorization: Bearer " . $this->accessToken;

        if ($this->hostname === "localhost")
        {
            $url = 'localhost/ns-api/';
        } else {
            $url = 'https://' . $this->hostname . '/ns-api/';
        }
        $response = $this->curl_post($url, $header, $params);
        return json_decode($response, true);
    }

    /**
     * @param string $url
     * @param array $header
     *
     * @return string
     */
    public function curl_get($url, array $header) {

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
    public function curl_post($url, array $header, array $vars) {

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
?>
