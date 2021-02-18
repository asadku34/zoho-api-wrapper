<?php

namespace Asad\Zoho\Abstracts;

use Asad\Zoho\ZohoApi;
use Asad\Zoho\Client\ZohoRequest;
use Asad\Zoho\Client\ZohoResponse;

abstract class RestApi
{
    public function __construct($cofig_id)
    {
        $this->setZohoApi(new ZohoApi($cofig_id));
    }

    /**
     * @param ZohoApi $zoho_api
     *
     * @return ZohoApi
     */
    public function setZohoApi(ZohoApi $zoho_api): RestApi
    {
        $this->zoho_api = $zoho_api;
        return $this;
    }

    /**
     * @return ZohoApi
     */
    public function getZohoApi(): ZohoApi
    {
        return $this->zoho_api;
    }

    /**
     * @param string $action = 'search'
     *
     * @param string $module = 'Leads'
     *
     * @param array $param = ['email' => 'test@gmail.com']
     *
     * @return ZohoRequest object
     */
    public function createRequest($action, $module, array $param): ZohoRequest
    {
        return new ZohoRequest($action, $module, $param);
    }

    /**
     * @param ZohoRequest $request
     *
     * @return ZohoApi response
     */
    public function makeRequest(ZohoRequest $request): ZohoResponse
    {
        return $this->getZohoApi()->get($request);
    }
}
