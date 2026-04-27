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
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'workflow_data')) {
                $table->json('workflow_data')->nullable()->after('status');
            }
            if (!Schema::hasColumn('sales_orders', 'tankers_data')) {
                $table->json('tankers_data')->nullable()->after('workflow_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['workflow_data', 'tankers_data']);
        });
    }
};
