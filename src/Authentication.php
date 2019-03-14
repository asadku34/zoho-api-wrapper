<?php
namespace Asad\Zoho;

use Illuminate\Database\Eloquent\Model;
use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Exception\ZohoException;
use Asad\Zoho\Models\ZohoOauthSetting;

class Authentication
{
    /**
     * Zoho Authentication API url base part
     */
    private $auth_api = 'https://accounts.zoho.com/oauth/v2/'; 
    /**
     * Api scope
     */
    private $scope = null;
    /**
     * Api access token
     */
    protected $access_token = null;
    /**
     * Configuration: Multiple authentication can be operated by define the configuration id.
     */
    private $config_id = null;

    public function __construct($config_id = null, string $scope = null)
    {
        $this->setScope($scope);
        $this->setAccessToken($config_id);
    }

    public function makeRedirectUrl($client_id, $redirect_route)
    {
        return $this->auth_api . "auth?scope=" . $this->getScope()
                . "&client_id=" . $client_id . "&response_type=code&access_type=offline&redirect_uri="
                . $redirect_route;
    }

    public function setScope(string $scope = null): Authentication
    {
        $this->scope = $scope ?? "ZohoCRM.modules.ALL,ZohoCRM.settings.ALL";
        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }
    /**
     * @param string $config_id
     */
    public function setAccessToken($config_id)
    {
        try {
            $this->validAccessToken($config_id);
        } catch (ZohoException $e) {
            echo $e->getMessage();
            exit;
        }
        
    }

    public function getAccessToken(): string 
    {
        return $this->access_token;
    }

    public function validAccessToken($config_id)
    {
        if ($config_id == null) {
            $setting = ZohoOauthSetting::orderBy('created_at', 'desc')->take(1)->first();
        } else {
            $setting = ZohoOauthSetting::find($config_id);
        }

        if (is_null($setting)) {
            throw new ZohoException("Zoho API package configuration is not found. Make sure you have executed the artisan command.");
        }

        $now = time() + 300; // Generate Refresh Token before five minutes.
        if($setting->expires_in_sec <= $now){
            try {
                $this->refreshAccessToken($setting);
            } catch (ZohoException $e) {
                echo $e->getMessage(); 
                echo "<br>It's seems something happened to your refresh token.";
                exit;
            }
        }else{
            $this->access_token = $setting->access_token;
        }

        return $this->access_token;
    }

    public function refreshAccessToken($setting)
    {
        $refreshToken 	= $setting->refresh_token;
        $client_id 		= $setting->client_id;
        $client_secret 	= $setting->client_secret;
        $id = $setting->id;
        
		$refresh_url	= $this->getRefreshTokenUrl($refreshToken, $client_id, $client_secret);
        $response = $this->getClient()->post($refresh_url)->getResults();
        
        if (isset($response->error)) {
            throw new ZohoException($response->error);
        }

        $setting = ZohoOauthSetting::find($id);
        $setting->access_token  = $response->access_token;
        $setting->expires_in    = $response->expires_in;
        $setting->expires_in_sec= $response->expires_in_sec + time();
        $setting->save();
        
        $this->access_token = $response->access_token;
		
    }

    public function getRefreshTokenUrl($refreshToken, $client_id, $client_secret)
    {
        $refresh_url_ext = 'token?refresh_token='.$refreshToken.'&client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=refresh_token';
        return $this->auth_api . $refresh_url_ext;
    }

    public function getClient()
    {
        return new ZohoClient();
    }

}