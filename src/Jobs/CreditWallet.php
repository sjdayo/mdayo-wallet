<?php

namespace Mdayo\Wallet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mdayo\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5; // Maximum attempts
    public $backoff = 10; // Delay in seconds before retrying
    
    public function __construct(
        protected Wallet $wallet,
        protected string $currency,
        protected float $amount,
        protected ?Model $ledgerable = null,
        protected array $meta = []
    ) {}

    public function handle()
    {
        $wallet_balance = $this->wallet->balances()->where('currency',$this->currency)->firstOrCreate(['currency' => $this->currency],['balance' => 0]);
        
        DB::transaction(function () use ($wallet_balance) {
            $wallet_balance->lockForUpdate();
            $wallet_balance->credit($this->amount, $this->ledgerable, $this->meta);
        });
    }
}
