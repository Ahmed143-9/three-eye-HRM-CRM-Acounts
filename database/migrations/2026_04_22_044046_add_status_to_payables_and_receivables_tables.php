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
        Schema::table('payables', function (Blueprint $table) {
            $table->string('status')->default('unpaid')->after('total_amount'); // unpaid, partial, paid
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->string('status')->default('unpaid')->after('total_amount'); // unpaid, partial, paid
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
