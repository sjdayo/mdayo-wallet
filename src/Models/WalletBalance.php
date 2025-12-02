<?php

namespace Mdayo\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Mdayo\Wallet\Exceptions\InsufficientBalanceException;

class WalletBalance extends Model
{
    protected $guarded = [];

    public function available()
    {
        return max(0,$this->balance - $this->frozen);
    }
    public function credit(float $amount): self
    {
        $this->increment('balance', $amount);   
        return $this->refresh();
    }

    /**
     * Debit the wallet balance (decrease available).
     */
    public function debit(float $amount): self
    {
        if ($this->balance < $amount) {
            throw new InsufficientBalanceException('Insufficient balance.');
        }
        $this->decrement('balance', $amount);
        return $this->refresh();
    }

    /**
     * Freeze a portion of the balance.
     */
    public function freeze(float $amount): self
    {
        if (($this->balance - $this->frozen) < $amount) {
            throw new InsufficientBalanceException('Insufficient available balance to freeze.');
        }
     
        $this->increment('frozen', $amount);
        return $this->refresh();
    }

    /**
     * Unfreeze a portion of the frozen balance.
     */
    public function unfreeze(float $amount): self
    {
        if ($this->frozen < $amount) {
            throw new InsufficientBalanceException('Not enough frozen balance to unfreeze.');
        }
        $this->decrement('frozen', $amount);
        return $this->refresh();
    }

    /**
     * Debit from frozen balance (used after successful escrow release).
     */
    public function debitFrozen(float $amount): self
    {   
        if ($this->frozen < $amount) {
            throw new InsufficientBalanceException('Insufficient frozen balance.');
        }
       
        $this->decrement('frozen', $amount);
        $this->decrement('balance', $amount);
        return $this->refresh();
    }
    public function currency()
    {
        return $this->morphTo();
    }
    
    public function wallet()
    {
        return $this->belongsTo(Wallet::class,'wallet_id');    
    }
   
 
}
