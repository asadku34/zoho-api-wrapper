<?php

namespace Asad\Zoho;

use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Exception\AuthenticationException;
use Asad\Zoho\Models\ZohoOauthSetting;

class Authentication
{
    //Default API Domain
    protected $api_domain = "https://www.zohoapis.com";
    private $ext = "crm/v2/";
    /**
     * Zoho Authentication API url base part
     */
    private $accounts_server = 'https://accounts.zoho.com';

    /**
     * Api access token
     */
    protected $access_token;
    /**
     * Configuration: Multiple authentication can be operated by define the configuration id.
     */
    private $config_id = null;
    /**
     * Zoho Oauth Setting
     */
    private $setting;

    /**
     * Tracking token expire before each call
     *
     * @var integer
     */
    protected $expires_in_sec;

    public function __construct($config_id = null)
    {
        $this->config_id = $config_id;
        $this->validateToken();
    }

    private function validateToken()
    {
        $this->setting = ZohoOauthSetting::getOauthById($this->config_id);

        if (!$this->setting) throw new AuthenticationException("Zoho API package configuration is not found. Make sure you have executed the artisan command.");

        $this->expires_in_sec = $this->setting->expires_in_sec;

        if ($this->expires_in_sec <= (time() + 60)) {
            $this->performTokenRefresh();
        }

        $this->access_token = $this->setting->access_token;
    }

    /**
     * @return void
     */
    private function performTokenRefresh()
    {
        try {
            $this->refreshAccessToken();
        } catch (AuthenticationException $e) {
            $additional_msg = "It's seems something happened to your refresh token.";
            throw new AuthenticationException($e->getMessage() . " - " . $additional_msg);
        }
    }

    private function refreshAccessToken()
    {
        $refresh_url    = $this->genRefreshTokenUrl();
        $response = $this->getClient()->post($refresh_url, 'refreshToken')->getResults();

        if (isset($response->error)) throw new AuthenticationException($response->error);

        $setting = ZohoOauthSetting::find($this->setting->id);

        if (!$setting) throw new AuthenticationException("Zoho API package configuration is not found. Make sure you have executed the artisan command.");

        $setting->access_token  = $response->access_token;
        $setting->expires_in    = $response->expires_in;
        $setting->expires_in_sec = isset($response->expires_in_sec) ? $response->expires_in_sec + time() : $response->expires_in + time();
        $setting->save();

        $this->setting = $setting;
    }

    private function genRefreshTokenUrl()
    {
        $elements = [
            "refresh_token=" . $this->setting->refresh_token,
            "client_id=" . $this->setting->client_id,
            "client_secret=" . $this->setting->client_secret,
            "grant_type=refresh_token",
        ];
        $query_param = implode("&", $elements);
        $auth_api = isset($this->setting->accounts_server) && $this->setting->accounts_server ? $this->setting->accounts_server : $this->accounts_server;
        return $auth_api . "/oauth/v2/token?" . $query_param;
    }
    /**
     * Get Access token outside of this class
     */
    public function getAccessToken(): String
    {
        if ($this->expires_in_sec <= (time() + 60)) {
            $this->performTokenRefresh();

            $this->access_token = $this->setting->access_token;
            $this->expires_in_sec = $this->setting->expires_in_sec;
        }
        return $this->access_token;
    }
    /**
     * Get Zoho api url outside of this class
     */
    public function getApiUrl()
    {
        try {
            $env = $this->connectTo();
            $api_url = $this->getApiDomain($env);
        } catch (AuthenticationException $e) {
            throw new AuthenticationException($e->getMessage());
        }
        return $api_url . "/" . $this->ext;
    }

    private function connectTo()
    {

        if (!isset($this->setting->connect_to)) throw new AuthenticationException("Make sure you have updated your oauth setting database.");

        if (!$this->setting->connect_to) throw new AuthenticationException("Make sure you have set the api environment variable.");

        return $this->setting->connect_to;
    }

    private function getApiDomain($env)
    {

        $this->api_domain = isset($this->setting->api_domain) && $this->setting->api_domain ? $this->setting->api_domain : $this->api_domain;
        $this->api_domain = $env != 'live' ? str_replace("www", "sandbox", $this->api_domain) : $this->api_domain;
        return $this->api_domain;
    }

    private function getClient()
    {
        return new ZohoClient();
    }
}
