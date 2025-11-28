<?php
namespace Mdayo\Wallet\Traits;
use Mdayo\Wallet\Models\Wallet;

trait HasWallet
{
    public function wallet()
    {
        return $this->morphOne(Wallet::class,'owner');
    }
    public function wallets()
    {
        return $this->morphMany(Wallet::class,'owner');
    }
}