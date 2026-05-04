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
        Schema::table('sales_cis', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_cis', 'client_ci_number')) {
                $table->string('client_ci_number')->nullable()->after('ci_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_cis', function (Blueprint $table) {
            $table->dropColumn('client_ci_number');
        });
    }
};
