<?php

namespace Mdayo\Wallet;

use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/wallet.php',
            'wallet'
        );
    }


    public function boot()
    {
        
        
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'wallet-migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/wallet.php' => config_path('wallet.php'),
        ], 'wallet-config');
   
    }
}
