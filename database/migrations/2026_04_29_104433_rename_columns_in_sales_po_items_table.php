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
        Schema::table('sales_po_items', function (Blueprint $table) {
            $table->renameColumn('unit', 'unit_id');
            $table->renameColumn('price', 'price_per_unit');
            $table->renameColumn('currency', 'currency_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_po_items', function (Blueprint $table) {
            $table->renameColumn('unit_id', 'unit');
            $table->renameColumn('price_per_unit', 'price');
            $table->renameColumn('currency_type', 'currency');
        });
    }
};
