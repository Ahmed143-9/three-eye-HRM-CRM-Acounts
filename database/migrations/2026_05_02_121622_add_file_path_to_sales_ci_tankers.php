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
            $table->string('file_path')->nullable()->after('total_amount_usd');
        });
    }

    public function down(): void
    {
        Schema::table('sales_ci_tankers', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
