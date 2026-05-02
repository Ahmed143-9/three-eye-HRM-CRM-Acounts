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
        Schema::table('sales_lcs', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_lcs', 'terms_and_conditions')) {
                $table->text('terms_and_conditions')->nullable()->after('port_of_discharge');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_lcs', function (Blueprint $table) {
            if (Schema::hasColumn('sales_lcs', 'terms_and_conditions')) {
                $table->dropColumn('terms_and_conditions');
            }
        });
    }
};
