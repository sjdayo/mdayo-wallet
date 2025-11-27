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
        Schema::create('wallet_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('currency');
            $table->decimal('balance', 24, 6)->default(0);
            $table->decimal('frozen', 24, 6)->default(0); // for holds/reserved funds
            $table->timestamps();

            // Ensure one token per wallet
            $table->unique(['wallet_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_balances');
    }
};
