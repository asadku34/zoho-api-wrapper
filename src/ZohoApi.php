<?php
namespace Asad\Zoho;

use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Client\ZohoRequest;
use Asad\Zoho\Client\ZohoResponse;
use Asad\Zoho\Authentication;

class ZohoApi
{
    private $client = null;
    private $api_url = 'https://www.zohoapis.com/crm/v2/';
    private $response = null;
    private $request = null;

    private $authentication = null;

    public function __construct($config_id = null, $scope = null)
    {
        $this->setClient();
        $this->setAuth(new Authentication($config_id, $scope));
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
        $this->authentication = $auth;
        return $this;
    }

    public function getAccessToken()
    {
        return $this->authentication->getAccessToken();
    }

    public function getUrl(): string
    {
        return $this->api_url . $this->request->getURI();
    }

    public function getRequestVerb()
    {
        return $this->request->getHttpVerb();
    }

    public function getJson()
    {
        return $this->request->getDataJson();
    }

    public function get(ZohoRequest $request): ZohoResponse
    {
        $this->setRequest($request);
        $access_token = $this->getAccessToken();
        $url = $this->getUrl();
        $http_verb = $this->getRequestVerb();
        $json_data = $this->getJson();

        $this->response = $this->client->execute($http_verb, $url, $access_token, $json_data);
        return $this->response;
    }

}