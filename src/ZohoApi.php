<?php

namespace Asad\Zoho;

use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Client\ZohoRequest;
use Asad\Zoho\Client\ZohoResponse;
use Asad\Zoho\Exception\AuthenticationException;
use Asad\Zoho\Models\ZohoOauthSetting;

class ZohoApi
{
    private $client = null;
    private $api_url = null;
    private $response = null;
    private $request = null;
    private $config_id = null;

    private $authentication = null;

    public function __construct($config_id = null, $scope = null)
    {
        $this->setClient();
        $this->setConfigId($config_id);
        $this->setAuth(new Authentication($config_id, $scope));
    }

    /**
     * Set API url. Now it can be controlled from your application.
     *
     * @return string zoho api url
     */
    public function getApiUrl(): string
    {
        try {
            $env = $this->getConnectTo($this->getConfigId());
            if ($env == 'live') {
                $env = 'www';
            }
        } catch (AuthenticationException $e) {
            throw new AuthenticationException($e->getMessage());
        }
        $this->api_url = 'https://'.$env.'.zohoapis.com/crm/v2/';

        return $this->api_url;
    }

    public function setConfigId($config_id)
    {
        $this->config_id = $config_id;
    }

    public function getConfigId()
    {
        return $this->config_id;
    }

    public function setClient(ZohoClient $client = null): self
    {
        $this->client = $client ?? new ZohoClient();

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setRequest(ZohoRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): ZohoRequest
    {
        return $this->request;
    }

    public function setAuth(Authentication $auth)
    {
        $this->authentication = $auth;

        return $this;
    }

    public function getAccessToken()
    {
        try {
            $this->authentication->setAccessToken($this->getConfigId());
        } catch (AuthenticationException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        return $this->authentication->getAccessToken();
    }

    /**
     * @param int Application configuration id
     *
     * @return string App Running Environment.[Sandbox or Live]
     */
    public function getConnectTo($config_id = null)
    {
        if ($config_id == null) {
            $setting = ZohoOauthSetting::orderBy('created_at', 'desc')->take(1)->first();
        } else {
            $setting = ZohoOauthSetting::find($config_id);
        }

        if (is_null($setting)) {
            throw new AuthenticationException('Zoho API package configuration is not found. Make sure you have executed the artisan command.');
        }

        if (!isset($setting->connect_to)) {
            throw new AuthenticationException('Make sure you have updated your oauth setting database.');
        }

        if (!$setting->connect_to) {
            throw new AuthenticationException('Make sure you have set the api environment variable.');
        }

        return $setting->connect_to;
    }

    public function getUrl(): string
    {
        return $this->getApiUrl().$this->request->getURI();
    }

    public function getRequestVerb()
    {
        return $this->request->getHttpVerb();
    }

    public function getJson()
    {
        return $this->request->getDataJson();
    }

    public function getAction()
    {
        return $this->request->getAction();
    }

    public function get(ZohoRequest $request): ZohoResponse
    {
        $this->setRequest($request);
        $access_token = $this->getAccessToken();
        $url = $this->getUrl();
        $http_verb = $this->getRequestVerb();
        $json_data = $this->getJson();
        $action = $this->getAction();

        $this->response = $this->client->execute($action, $http_verb, $url, $access_token, $json_data);

        return $this->response;
    }
}
