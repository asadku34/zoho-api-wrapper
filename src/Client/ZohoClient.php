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

    public function execute($action, $http_verb, $url, $access_token, array $data = null)
    {

        if ($data !== null) {
            $data_header['json'] = $data;
        }

        if (isset($data['multipart'])) {
            $data_header = $data;
        }

        $data_header['headers'] = [
                'Accept' => 'application/json',
                'Content-Length' => '0',
                'Authorization' => 'Zoho-oauthtoken '.$access_token
        ];

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