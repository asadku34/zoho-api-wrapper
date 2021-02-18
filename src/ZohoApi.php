<?php

namespace Asad\Zoho;

use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Client\ZohoRequest;
use Asad\Zoho\Client\ZohoResponse;
use Asad\Zoho\Authentication;

class ZohoApi
{
    private $client = null;
    private $request = null;
    private $response = null;
    private $_auth = null;

    public function __construct($config_id = null)
    {
        $this->setClient();
        $this->setAuth(new Authentication($config_id));
    }

    public function setClient(ZohoClient $client = null): ZohoApi
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
        $this->_auth = $auth;
        return $this;
    }

    public function apiEndpoint(): string
    {
        return $this->_auth->getApiUrl() . $this->request->getURI();
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
        $access_token = $this->_auth->getAccessToken();
        $url = $this->apiEndpoint();
        $http_verb = $this->getRequestVerb();
        $json_data = $this->getJson();
        $action = $this->getAction();

        $this->response = $this->client->execute($action, $http_verb, $url, $access_token, $json_data);
        return $this->response;
    }
}
