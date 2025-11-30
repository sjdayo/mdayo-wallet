<?php

namespace Mdayo\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLedger extends Model
{
    protected $guarded = [];
    public function walletBalance()
    {
        return $this->belongsTo(WalletBalance::class);
    }
   
    public function currency()
    {
        return $this->morphTo();
    }
    
    public function ledgerable()
    {
        return $this->morphTo();
    }
}
