<?php

namespace RealSoft\FEL;

use Illuminate\Support\ServiceProvider;
use RealSoft\FEL\Contracts\CountryAdapter;
use RealSoft\FEL\Contracts\CertifierDriver;
use RealSoft\FEL\Validators\Registry;
use RealSoft\FEL\Adapters\GT\GTAdapter;
use RealSoft\FEL\Adapters\SV\SVAdapter;
use RealSoft\FEL\Drivers\Infile\InfileGuatemalaDriver;
use RealSoft\FEL\Drivers\Infile\InfileElSalvadorDriver;

class FELServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fel.php', 'fel');

        $this->app->singleton(Registry::class, function(){
            return new Registry(resource_path('vendor/realsoft-fel/schemas'));
        });

        $this->app->bind(CountryAdapter::class, function($app){
            $country = config('fel.default_country', 'GT');
            return $country === 'SV' ? new SVAdapter() : new GTAdapter();
        });

        $this->app->bind(CertifierDriver::class, function($app){
            $provider = config('fel.default_provider', 'infile');
            $country  = config('fel.default_country', 'GT');
            if ($provider === 'infile') {
                return $country === 'SV' ? new InfileElSalvadorDriver() : new InfileGuatemalaDriver();
            }
            throw new \RuntimeException('No certifier driver configured');
        });

        $this->app->singleton(FELManager::class, function($app){
            return new FELManager(
                $app->make(CountryAdapter::class),
                $app->make(CertifierDriver::class),
                null // signer: null => firma en proveedor por defecto
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/fel.php' => config_path('fel.php'),
            __DIR__.'/../src/Schema' => resource_path('vendor/realsoft-fel/schemas'),
        ], 'realsoft-fel-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/fel.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fel');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fel');
    }
}
