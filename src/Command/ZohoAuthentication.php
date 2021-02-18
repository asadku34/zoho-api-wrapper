<?php

namespace Asad\Zoho\Command;

use Illuminate\Console\Command;

use Asad\Zoho\Models\ZohoOauthSetting;

class ZohoAuthentication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:authentication';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Zoho Authorization Request URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client_id = $this->ask('Input CRM client id');
        $zoho_setting = ZohoOauthSetting::where('client_id', $client_id)->first();

        if (!is_null($zoho_setting) && $zoho_setting->access_token != null) {
            if (!$this->confirm('Access token already exist. Do you want to continue?')) {
                exit;
            }
        }

        $client_secret  = $this->ask('Input CRM client secret');
        $client_domain  = $this->ask('Input client domain (ex: example.com)');
        $protocol       = $this->choice('Select your protocol.', ['http', 'https'], 0);
        $environment    = $this->choice('Select your env.', ['sandbox', 'live'], 0);
        $cn_dc             = $this->choice('Is it CN DC?', ['No', "Yes"], 0);

        $scopes = $this->ask('Scopes? (ex: ZohoCRM.modules.ALL,ZohoCRM.settings.ALL) If you skip it will enable ex scopes');

        $api_scopes = "ZohoCRM.modules.ALL,ZohoCRM.settings.ALL";
        if ($scopes) {
            $api_scopes = $scopes;
        }

        $base = "https://accounts.zoho.com";

        if ($cn_dc == "Yes") {
            $base = "https://accounts.zoho.com.cn";
        }

        $elements = [
            $protocol,
            '://',
            rtrim($client_domain, '/'),
            '/',
            config('zoho.redirect_to')
        ];
        $redirect_route =  implode("", $elements);

        $params = [
            "scope={$api_scopes}",
            "client_id={$client_id}",
            "response_type=code",
            "access_type=offline",
            "redirect_uri={$redirect_route}",
        ];

        $redirect_url = $base . '/oauth/v2/auth?' . implode("&", $params);

        $flight = ZohoOauthSetting::updateOrCreate(
            ['client_id' => $client_id],
            [
                'client_id'         => $client_id,
                'client_secret'     => $client_secret,
                'client_domain'     => $client_domain,
                'protocol'          => $protocol,
                'connect_to'        => $environment,
            ]
        );

        $this->info('Copy the following url, paste on browser and hit return.');
        $this->line($redirect_url);
    }
}
