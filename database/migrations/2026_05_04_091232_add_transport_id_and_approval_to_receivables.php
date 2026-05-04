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
        Schema::table('receivables', function (Blueprint $table) {
            if (!Schema::hasColumn('receivables', 'transport_id')) {
                $table->unsignedBigInteger('transport_id')->nullable()->after('ci_id');
            }
        });
        
        Schema::table('payables', function (Blueprint $table) {
            if (!Schema::hasColumn('payables', 'transport_id')) {
                $table->unsignedBigInteger('transport_id')->nullable()->after('ci_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn('transport_id');
        });
        
        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn('transport_id');
        });
    }
};
