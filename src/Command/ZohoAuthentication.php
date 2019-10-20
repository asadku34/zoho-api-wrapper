<?php

namespace Asad\Zoho\Command;

use Asad\Zoho\Models\ZohoOauthSetting;
use Illuminate\Console\Command;

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
    protected $description = 'Command description';

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

        $client_secret = $this->ask('Input CRM client secret');
        $client_domain = $this->ask('Input client domain (ex: example.com)');
        $protocol = $this->choice('Select your protocol.', ['http', 'https'], 0);
        $environment = $this->choice('Select your env.', ['sandbox', 'live'], 0);
        $redirect_route = $protocol.'://'.rtrim($client_domain, '/').'/oauth2back';

        $redirect_url = 'https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL&client_id='.$client_id.'&response_type=code&access_type=offline&redirect_uri='.$redirect_route;

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
