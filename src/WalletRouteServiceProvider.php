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
        $path = base_path('routes/wallet.php');

        if (file_exists($path)) {
            // Use published routes if they exist
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group($path);
        } else {
            // Otherwise, use package default
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(__DIR__.'/../routes/wallet.php');
        }
    }
}
