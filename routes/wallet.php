<?php

use Illuminate\Support\Facades\Route;
use Mdayo\Wallet\Http\Controllers\WalletController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('wallets', [WalletController::class, 'index']);
    Route::get('wallets/{wallet}', [WalletController::class, 'show']);
    Route::get('wallets/{wallet}/balance/{currency}', [WalletController::class, 'balance']);
    Route::get('wallets/{wallet}/ledger/{currency}', [WalletController::class, 'ledger']);
});