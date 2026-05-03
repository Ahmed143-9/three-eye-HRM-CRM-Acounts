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
            if (!Schema::hasColumn('sales_po_items', 'currency')) {
                if (Schema::hasColumn('sales_po_items', 'price')) {
                    $table->string('currency')->nullable()->after('price');
                } elseif (Schema::hasColumn('sales_po_items', 'price_per_unit')) {
                    $table->string('currency')->nullable()->after('price_per_unit');
                } else {
                    $table->string('currency')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_po_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_po_items', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
