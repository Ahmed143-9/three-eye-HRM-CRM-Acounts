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
        Schema::table('sales_purchase_orders', function (Blueprint $table) {
            $table->string('prepared_by')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('acknowledged_by')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['prepared_by', 'issued_by', 'acknowledged_by', 'status']);
        });
    }
};
