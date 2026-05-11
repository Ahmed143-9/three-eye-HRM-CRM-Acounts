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
        Schema::create('petty_cash_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('petty_cash_allocation_id');
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('purpose');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('petty_cash_allocation_id')->references('id')->on('petty_cash_allocations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_usages');
    }
};
