<?php

namespace PhpJunior\Laravel2C2P;

use Illuminate\Support\ServiceProvider;
use PhpJunior\Laravel2C2P\Api\PaymentGatewayApi;
use PhpJunior\Laravel2C2P\Encryption\Encryption;
use PhpJunior\Laravel2C2P\Facades\EncryptionFacades;
use PhpJunior\Laravel2C2P\Facades\Laravel2C2PFacades;

class Laravel2C2PServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath()      => config_path('laravel-2c2p.php'),
            $this->certificatePath() => storage_path('cert'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'laravel-2c2p');
        $this->registerFacade();
        $this->registerEncryption();
        $this->register2C2P();
    }

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__.'/../config/laravel-2c2p.php';
    }

    /**
     * @return string
     */
    protected function certificatePath()
    {
        return __DIR__.'/../storage/cert';
    }

    private function registerFacade()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Payment2C2P', Laravel2C2PFacades::class);
        });

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Encryption', EncryptionFacades::class);
        });
    }

    private function registerEncryption()
    {
        $this->app->bind('2c2p-encryption', function ($app) {
            $config = $app['config'];

            return new Encryption($config);
        });
    }

    private function register2C2P()
    {
        $this->app->bind('2c2p-payment-gateway-api', function ($app) {
            $config = $app['config'];
            $encryption = $app['2c2p-encryption'];

            return new PaymentGatewayApi($config, $encryption);
        });
    }
}
