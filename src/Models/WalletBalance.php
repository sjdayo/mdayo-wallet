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
    public function credit(float $amount,Model $ledgerable,?array $meta): self
    {
        $balance_before = $this->available();
        $this->increment('balance', $amount);
        $balance_after = $this->available();
        
        $this->createLedger('credit',$ledgerable,$amount,$balance_before,$balance_after,$meta);
        return $this->refresh();
    }

    /**
     * Debit the wallet balance (decrease available).
     */
    public function debit(float $amount,Model $ledgerable,?array $meta): self
    {
        if ($this->balance < $amount) {
            throw new InsufficientBalanceException('Insufficient balance.');
        }
        
        $balance_before = $this->available();
        $this->decrement('balance', $amount);
        $balance_after = $this->available();
        
        $this->createLedger('debit',$ledgerable,$amount,$balance_before,$balance_after,$meta);
        return $this->refresh();
    }

    /**
     * Freeze a portion of the balance.
     */
    public function freeze(float $amount,Model $ledgerable,?array $meta): self
    {
        if (($this->balance - $this->frozen) < $amount) {
            throw new InsufficientBalanceException('Insufficient available balance to freeze.');
        }
        $balance_before = $this->available();
        $this->increment('frozen', $amount);
        $balance_after = $this->available();
        
        $this->createLedger('debit',$ledgerable,$amount,$balance_before,$balance_after,$meta);
        return $this->refresh();
    }

    /**
     * Unfreeze a portion of the frozen balance.
     */
    public function unfreeze(float $amount,Model $ledgerable,?array $meta): self
    {
        if ($this->frozen < $amount) {
            throw new InsufficientBalanceException('Not enough frozen balance to unfreeze.');
        }
        $balance_before = $this->available();
        $this->decrement('frozen', $amount);
        $balance_after = $this->available();
        
        $this->createLedger('credit',$ledgerable,$amount,$balance_before,$balance_after,$meta);
        return $this->refresh();
    }

    /**
     * Debit from frozen balance (used after successful escrow release).
     */
    public function debitFrozen(float $amount,Model $ledgerable,?array $meta): self
    {   
        if ($this->frozen < $amount) {
            throw new InsufficientBalanceException('Insufficient frozen balance.');
        }
        $balance_before = $this->available();
        
        $this->decrement('frozen', $amount);
        $this->decrement('balance', $amount);
        
        $balance_after = $this->available();
        
        $this->createLedger('debit',$ledgerable,$amount,$balance_before,$balance_after,$meta);
        return $this->refresh();
    }
    
    public function wallet()
    {
        return $this->belongsTo(Wallet::class,'wallet_id');    
    }
    public function ledger()
    {
        return $this->hasMany(WalletLedger::class,'wallet_balance_id');    
    }
    private function createLedger(string $type,Model $ledgerable, float $amount, $balance_before,$balance_after,array $meta = [])
    {
        
        return $this->ledger()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balance_before,
            'balance_after' => $balance_after,
            'ledgerable_id' => $ledgerable->id,
            'ledgerable_type' => get_class($ledgerable),
            'meta' => $meta
        ]);
    }   

}
