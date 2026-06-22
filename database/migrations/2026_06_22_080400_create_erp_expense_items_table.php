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
        if (!Schema::hasTable('erp_expense_items')) {
            Schema::create('erp_expense_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('erp_expense_id');
                $table->string('product_name');
                $table->decimal('quantity', 15, 2)->default(1);
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('erp_expense_id')->references('id')->on('erp_expenses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_expense_items');
    }
};
