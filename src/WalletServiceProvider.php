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
        // Merge queue config for RabbitMQ if needed
        $this->mergeConfigFrom(
            __DIR__.'/../config/queue.php',
            'queue.connections.wallet_rabbitmq'
        );
    }


    public function boot()
    {
        // Load package routes
        $this->loadRoutesFrom(__DIR__.'/../routes/wallet.php');
            // Optional: allow publishing routes for customization
        $this->publishes([
            __DIR__ . '/../routes/wallet.php' => base_path('routes/wallet.php')
        ], 'wallet-routes');
        
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'wallet-migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/wallet.php' => config_path('wallet.php'),
        ], 'wallet-config');
        
         // Optional: publish queue config if you want
        $this->publishes([
            __DIR__.'/../config/queue.php' => config_path('queue.php'),
        ], 'wallet-queue-config');
    }
}
