<?php

namespace Asad\Zoho\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Exception\ZohoException;
use Asad\Zoho\Models\ZohoOauthSetting;

use function GuzzleHttp\json_encode;

class ZohoController extends Controller
{
    private $code;
    private $location;
    private $accounts_server = 'https://accounts.zoho.com';
    private $error;
    private $setting_id;

    public function oauth2back(Request $request)
    {
        $this->code = $request->code;
        $this->location = $request->location;
        $this->accounts_server = $request->{'accounts-server'};

        if (!$this->code) return $this->response("Authorization Code is missing.");

        $request_url = $this->getAccessTokenGenUrl();

        if (!$request_url) return $this->response("Zoho Oauth2 user is missing in database.");

        $response = $this->generateToken($request_url);

        if (isset($response->error)) return $this->response($response->error);

        if ($this->error) return $this->response($this->error);

        $result = $this->updateOauth2DB($response);

        if (!$result) return $this->response("Failed to update Oauth2 information in database.");

        return $this->response("You have successfully generated the access token. Now you can make API request", 'success');
    }

    public function generateToken($request_url)
    {
        try {
            $api_reaponse = $this->getClient()->post($request_url);
            $api_reaponse = $api_reaponse->getResults();
        } catch (ZohoException $e) {
            $api_reaponse = false;
            $this->error = $e->getMessage();
        }
        return $api_reaponse;
    }

    public function getAccessTokenGenUrl()
    {
        $setting = $this->getSetting();

        if (!$setting) return "";

        $this->setting_id = $setting->id;

        $client_id = $setting->client_id;
        $client_secret = $setting->client_secret;
        $params = [
            "code={$this->code}",
            "redirect_uri={$this->getRedirectUrl($setting)}",
            "client_id={$client_id}",
            "client_secret={$client_secret}",
            "grant_type=authorization_code"
        ];

        $base = strtolower($this->location) != 'cn' ? $this->accounts_server : 'https://accounts.zoho.com.cn';

        return $base . "/oauth/v2/token?" . implode("&", $params);
    }

    public function getRedirectUrl($setting)
    {
        if (!$setting) return "";

        $elements = [
            $setting->protocol,
            "://",
            rtrim($setting->client_domain, '/'),
            "/",
            config('zoho.redirect_to')
        ];

        return implode("", $elements);
    }

    public function getSetting()
    {
        return ZohoOauthSetting::orderBy('created_at', 'desc')->take(1)->first();
    }

    public function updateOauth2DB($response)
    {
        if (isset($response->access_token)) {

            $data['accounts_server'] = $this->accounts_server;
            $data['access_token']    = $response->access_token;
            $data['api_domain']      = $response->api_domain;
            $data['token_type']      = $response->token_type;
            $data['expires_in']      = $response->expires_in;
            $data['expires_in_sec']  = isset($response->expires_in_sec) ? $response->expires_in_sec + time() : $response->expires_in + time();

            if (isset($response->refresh_token)) {
                $data['refresh_token']  = $response->refresh_token;
            }

            $result = ZohoOauthSetting::updateOrCreate(
                [
                    'id' => $this->setting_id,
                ],
                $data
            );

            return $result ? true : false;
        }
        return false;
    }

    public function response($message, $status = 'failed')
    {
        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function getClient()
    {
        return new ZohoClient();
    }

    public function zoho()
    {
        return "from zoho api wrapper";
    }
}
