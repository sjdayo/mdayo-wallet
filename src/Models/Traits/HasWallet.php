<?php
namespace Mdayo\Wallet\Models\Traits;
use Mdayo\Wallet\Models\Wallet;

trait HasWallet
{
    protected static function bootHasWallet()
    {
        static::saved(function ($owner) {
            if (!$owner->wallet()->exists()) 
            {
                $owner->wallet()->create();
            }
        });
    }
    public function wallet()
    {
        return $this->morphOne(Wallet::class,'owner');
    }
    public function wallets()
    {
        return $this->morphMany(Wallet::class,'owner');
    }
}