<?php
namespace Asad\Zoho\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Asad\Zoho\Client\ZohoResponse;
use Asad\Zoho\Exception\ZohoException;

class ZohoClient
{
    private $client;
    public function __construct()
    {
        $this->setClient(new Client());
    }

     /*
     * @param Guzzle Client
     *
     * @return Zoho Client
     *  */

    public function setClient($client)
    {
        $this->client = $client;

        return $this->client;
    }

    public function prepareDataHeader($access_token)
    {
        return [
            'Accept' => 'application/json',
            'Content-Length' => '0',
            'Authorization' => 'Zoho-oauthtoken '.$access_token
        ];
    }

    public function getDataHeader($access_token, $data = null)
    {
        $data_headers['headers'] = $this->prepareDataHeader($access_token);
        if (isset($data['headers'])) {
            $data_headers['headers'] = array_merge($data_headers['headers'], $data['headers']);
            unset($data['headers']);
        }

        if (isset($data['multipart'])) {
            $data_headers = array_merge($data_headers, $data);
            unset($data['multipart']);
        } elseif ($data) {
            $data_headers['json'] = $data;
        }

        return $data_headers;

    }

    public function execute($action, $http_verb, $url, $access_token, array $data = null)
    {

        $data_header = $this->getDataHeader($access_token, $data);

        try {
            $res = $this->client->request($http_verb, $url, $data_header);
        } catch (GuzzleRequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
            }else{
                throw new ZohoException($e->getMessage());
            }
        }
        return new ZohoResponse($res, $action);
    }

    public function get(string $url, string $access_token)
    {
        $action = 'get';
        try {
            $res = $this->client->request('GET', $url);
        } catch (GuzzleRequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
            }else{
                throw new ZohoException($e->getMessage());
            }
        }
        return new ZohoResponse($res, $action);
    }

    public function post($url, $action='post')
    {
        try {
            $res = $this->client->request('POST', $url);
        } catch (GuzzleRequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
            }else{
                throw new ZohoException($e->getMessage());
            }
        }
        return new ZohoResponse($res, $action);
    }

}