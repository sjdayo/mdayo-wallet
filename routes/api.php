<?php

use Illuminate\Support\Facades\Route;
use Mdayo\Wallet\Http\Controllers\WalletController;

Route::middleware('auth:sanctum')->prefix('wallets')->group(function () {

    // List all wallets of authenticated user
    Route::get('/', [WalletController::class, 'index'])->name('wallets.index');

    // Show a specific wallet
    Route::get('/{wallet}', [WalletController::class, 'show'])->name('wallets.show');

    // Get balance of a wallet by currency
    Route::get('/{wallet}/balances/{currency}', [WalletController::class, 'balance'])->name('wallets.balance');

    // Get ledger of a wallet by currency (paginated)
    Route::get('/{wallet}/ledger/{currency}', [WalletController::class, 'ledger'])->name('wallets.ledger');

});
