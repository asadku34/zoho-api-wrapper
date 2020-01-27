<?php

namespace Asad\Zoho;

use Illuminate\Support\Facades\Route;
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
        $this->registerResources();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register service here
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    private function registerResources()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        });
    }
    /**
     * Get the Press route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'prefix' => '/',
            'namespace' => 'Asad\Zoho\Controllers',
        ];
    }


}
