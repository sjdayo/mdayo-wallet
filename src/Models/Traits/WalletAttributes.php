<?php
namespace Mdayo\Wallet\Models\Traits;
use Illuminate\Support\Str;
use Mdayo\Wallet\Models\WalletBalance;

trait WalletAttributes {
    
    protected static function bootWalletAttributes()
    {
        parent::boot();

        static::creating(function ($wallet) {
            if (empty($wallet->digital_address)) {
                $wallet->digital_address = static::generateUniqueDigitalAddress();
            }
        });
    }
    public function owner()
    {
        return $this->morphTo();
    }
    protected static function generateUniqueDigitalAddress()
    {
        do {
            $randomPart = Str::upper(Str::random(10));
            $walletAddress =  config('wallet.digital_address_prefix').$randomPart;
        } while (self::where('digital_address', $walletAddress)->exists());

        return $walletAddress;
    }

    public function balances()
    {
        return $this->hasMany(WalletBalance::class);
    }
}