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
        Schema::create('petty_cash_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7); // e.g., '2026-05'
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->decimal('rollover_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('allocated_by')->nullable(); // admin id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_allocations');
    }
};
