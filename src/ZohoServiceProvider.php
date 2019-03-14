<?php

namespace Asad\Zoho;

use Illuminate\Support\ServiceProvider;
use Asad\Zoho\Command\ZohoAuthentication;
class ZohoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                ZohoAuthentication::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        include __DIR__.'/ZohoApi.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Asad\Zoho\Controllers\ZohoController');
    }
}
