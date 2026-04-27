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
        Schema::table('sales_ci_tankers', function (Blueprint $table) {
            $table->string('quantity_unit')->nullable()->after('quantity_mt');
            $table->string('currency')->nullable()->after('cpt_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_ci_tankers', function (Blueprint $table) {
            $table->dropColumn(['quantity_unit', 'currency']);
        });
    }
};
