<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_lcs', function (Blueprint $table) {
            // Rename lc_no → lc_reference_no
            if (Schema::hasColumn('sales_lcs', 'lc_no') && !Schema::hasColumn('sales_lcs', 'lc_reference_no')) {
                $table->renameColumn('lc_no', 'lc_reference_no');
            }
            // Rename client_lc_number → client_lc_no
            if (Schema::hasColumn('sales_lcs', 'client_lc_number') && !Schema::hasColumn('sales_lcs', 'client_lc_no')) {
                $table->renameColumn('client_lc_number', 'client_lc_no');
            }
            // Rename amount → lc_qty
            if (Schema::hasColumn('sales_lcs', 'amount') && !Schema::hasColumn('sales_lcs', 'lc_qty')) {
                $table->renameColumn('amount', 'lc_qty');
            }
        });

        Schema::table('sales_lcs', function (Blueprint $table) {
            // Add lc_type if missing
            if (!Schema::hasColumn('sales_lcs', 'lc_type')) {
                $table->string('lc_type')->nullable()->after('lc_reference_no');
            }
            // Add unit (readonly, from PO) if missing
            if (!Schema::hasColumn('sales_lcs', 'unit')) {
                $table->string('unit')->nullable()->after('lc_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_lcs', function (Blueprint $table) {
            if (Schema::hasColumn('sales_lcs', 'lc_reference_no') && !Schema::hasColumn('sales_lcs', 'lc_no')) {
                $table->renameColumn('lc_reference_no', 'lc_no');
            }
            if (Schema::hasColumn('sales_lcs', 'client_lc_no') && !Schema::hasColumn('sales_lcs', 'client_lc_number')) {
                $table->renameColumn('client_lc_no', 'client_lc_number');
            }
            if (Schema::hasColumn('sales_lcs', 'lc_qty') && !Schema::hasColumn('sales_lcs', 'amount')) {
                $table->renameColumn('lc_qty', 'amount');
            }
        });

        Schema::table('sales_lcs', function (Blueprint $table) {
            if (Schema::hasColumn('sales_lcs', 'lc_type')) {
                $table->dropColumn('lc_type');
            }
            if (Schema::hasColumn('sales_lcs', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
