<?php
namespace Mdayo\Wallet\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $guarded = [];
    
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    protected static function boot()
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
    public static function generateUniqueDigitalAddress()
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
    public function ledgers()
    {
        return $this->hasManyThrough(
            WalletLedger::class,      // Final model
            WalletBalance::class,     // Intermediate model
            'wallet_id',              // FK on WalletBalance
            'wallet_balance_id',      // FK on WalletLedger
            'id',                     // Local key on Wallet
            'id'                      // Local key on WalletBalance
        );
    }

  
}
