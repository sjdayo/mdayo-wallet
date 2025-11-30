<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->string('digital_address', 64)->unique()->nullable();
            $table->timestamps();
        });
        Schema::create('wallet_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->morphs('currency');
            $table->decimal('balance', 24, 6)->default(0);
            $table->decimal('frozen', 24, 6)->default(0); // for holds/reserved funds
            $table->timestamps();

            // Ensure one token per wallet
            $table->unique(['wallet_id', 'currency']);
        });
         Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            // Polymorphic relation to any origin (Transaction, Order, Payment)
            $table->nullableMorphs('ledgerable'); // ledgerable_id + ledgerable_type
            $table->foreignId('wallet_balance_id')->constrained('wallet_balances')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->morphs('currency');
            $table->bigInteger('amount');
            $table->bigInteger('balance_before');
            $table->bigInteger('balance_after');            
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('wallet_balances');
        Schema::dropIfExists('wallet_ledgers');

    }
};
