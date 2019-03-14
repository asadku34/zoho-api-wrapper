<?php
namespace Asad\Zoho\Controllers;

use Illuminate\Http\Request;
use Asad\Zoho\Client\ZohoClient;
use Asad\Zoho\Exception\ZohoException;
use Asad\Zoho\Models\ZohoOauthSetting;
use App\Http\Controllers\Controller;


class ZohoController extends Controller
{
    
    public function oauth2back(Request $request)
    {

        if($request->input('code')){
            $zoho_setting = ZohoOauthSetting::orderBy('created_at', 'desc')->take(1)->first();
			
			if($zoho_setting){
                $code           = $request->input('code');
                $redirect_url   = 'http://' . $zoho_setting->client_domain .'/oauth2back';
                $client_id      = $zoho_setting->client_id;
                $client_secret  = $zoho_setting->client_secret;

				$request_url 	= 'https://accounts.zoho.com/oauth/v2/token?code='.$code.'&redirect_uri='.$redirect_url.'&client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=authorization_code';
                
                $crm_response = $this->getClient()->post($request_url)->getResults();
        
                if (isset($crm_response->error)) {
                    throw new ZohoException($crm_response->error);
                }
                
				if($crm_response != null && isset($crm_response->access_token)){

                    $data['access_token']   = $crm_response->access_token;
                    $data['api_domain']     = $crm_response->api_domain;
                    $data['token_type']     = $crm_response->token_type;
                    $data['expires_in']      = $crm_response->expires_in;
                    $data['expires_in_sec']  = $crm_response->expires_in_sec + time();
                    
                    if (isset($crm_response->refresh_token)) {
                        $data['refresh_token']  = $crm_response->refresh_token;
                    }

					$result = ZohoOauthSetting::updateOrCreate(
                                                [
                                                    'id' => $zoho_setting->id, //This is condition
                                                ],
                                                $data // data array
                                            );

                    $response = ['status' => 'success', 'message' => 'You have successfully generated the access token. Now you can make API request.'];
                    if (!$result) {
                        $response['status'] = 'failed';
                        $response['message'] = 'You have failed to generate the access token. Please check and try again.';
                    }
                    
                    return json_encode($response);
				}
			}
		}

    }

    public function getClient()
    {
        return new ZohoClient();
    }

}
