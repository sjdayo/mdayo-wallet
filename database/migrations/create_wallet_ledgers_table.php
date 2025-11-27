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
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            // Polymorphic relation to any origin (Transaction, Order, Payment)
            $table->nullableMorphs('ledgerable'); // ledgerable_id + ledgerable_type
            $table->foreignId('wallet_balance_id')->constrained('wallet_balances')->cascadeOnDelete();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('wallet_ledgers');
    }
};
