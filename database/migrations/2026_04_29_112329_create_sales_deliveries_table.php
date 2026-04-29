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
        Schema::create('sales_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('delivery_mode')->nullable();
            $table->string('packing_type')->nullable();
            $table->decimal('total_quantity_mt', 15, 3)->default(0);
            $table->decimal('total_quantity_kg', 15, 3)->default(0);
            $table->decimal('required_units', 15, 2)->default(0);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_deliveries');
    }
};
