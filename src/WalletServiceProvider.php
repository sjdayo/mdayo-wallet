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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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
