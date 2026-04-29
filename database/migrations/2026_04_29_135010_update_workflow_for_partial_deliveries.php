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
        // Update Sales Packing Lists to belong to CI
        Schema::table('sales_packing_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_packing_lists', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('order_id');
            }
        });

        // Update Sales Consignment Notes to belong to CI
        Schema::table('sales_consignment_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_consignment_notes', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('order_id');
            }
        });

        // Update Sales Deliveries to belong to CI
        Schema::table('sales_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_deliveries', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('order_id');
            }
        });

        // Update Transports to belong to CI
        Schema::table('transports', function (Blueprint $table) {
            if (!Schema::hasColumn('transports', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('sales_order_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_packing_lists', function (Blueprint $table) {
            $table->dropColumn('ci_id');
        });

        Schema::table('sales_consignment_notes', function (Blueprint $table) {
            $table->dropColumn('ci_id');
        });

        Schema::table('sales_deliveries', function (Blueprint $table) {
            $table->dropColumn('ci_id');
        });

        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('ci_id');
        });
    }
};
