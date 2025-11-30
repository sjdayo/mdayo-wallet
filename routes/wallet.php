<?php

use Illuminate\Support\Facades\Route;
use Mdayo\Wallet\Http\Controllers\WalletController;

Route::middleware('auth:sanctum')->prefix('wallets')->group(function () {
    Route::get('/', [WalletController::class, 'index']);
    Route::get('/{digital_address}', [WalletController::class, 'show']);
    Route::get('/{digital_address}/ledger', [WalletController::class, 'ledger']);
});