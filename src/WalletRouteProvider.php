<?php

namespace Mdayo\Wallet;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class WalletRouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     */
    protected $namespace = 'Mdayo\Wallet\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();

        // Optional: route model binding
        Route::model('wallet', \Mdayo\Wallet\Models\Wallet::class);
    }

    /**
     * Define the routes for the Wallet package.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define API routes
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api') // or 'auth:sanctum' if you want
            ->namespace($this->namespace)
            ->group(__DIR__.'/../routes/wallet.php'); // path to your package api routes
    }
}
