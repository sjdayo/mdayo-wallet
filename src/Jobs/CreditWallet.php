<?php

namespace Mdayo\Wallet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Mdayo\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class CreditWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum attempts
     */
    public $tries;

    /**
     * Backoff intervals
     */
    public $backoff;

    /**
     * Create a new job instance.
     *
     * @param Wallet $wallet
     * @param Model|null $currency
     * @param float $amount
     * @param Model|null $ledgerable
     * @param array $meta
     */
    public function __construct(
        protected Wallet $wallet,
        protected ?Model $currency = null,
        protected float $amount,
        protected ?Model $ledgerable = null,
        protected array $meta = []
    ) {
        $this->tries = config('wallet-queue.queue.tries', 5);
        $this->backoff = config('wallet-queue.queue.backoff', [5, 15, 30]);
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        // Ensure the wallet balance exists
        $wallet_balance = $this->wallet->balances()->firstOrCreate(
            ['currency_id' => $this->currency->id,'currency_type'=>get_class($this->currency)],
            ['balance' => 0]
        );

        // Transaction + row locking to prevent race conditions
        DB::transaction(function () use ($wallet_balance) {

            $wallet_balance->lockForUpdate();

            // Perform the debit
            $wallet_balance->credit(
                $this->amount,
                $this->ledgerable,
                $this->meta
            );
        });
    }

    /**
     * Handle a failed job.
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        \Log::error(
            "CreditWallet job failed for wallet ID {$this->wallet->id} ({$this->currency}): {$exception->getMessage()}",
            [
                'wallet_id' => $this->wallet->id,
                'currency' => $this->currency,
                'amount' => $this->amount,
                'ledgerable_type' => $this->ledgerable ? get_class($this->ledgerable) : null,
                'ledgerable_id' => $this->ledgerable?->id,
            ]
        );
    }

    /**
     * Tags for monitoring
     */
    public function tags(): array
    {
        return [
            'wallet',
            "wallet:{$this->wallet->id}",
            "currency:{$this->currency}",
        ];
    }
}
