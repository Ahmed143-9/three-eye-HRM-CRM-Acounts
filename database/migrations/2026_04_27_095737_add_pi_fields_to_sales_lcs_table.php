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
            $table->string('lifting_time')->nullable()->after('latest_shipment_date');
            $table->string('country_of_origin')->nullable()->after('lifting_time');
            $table->string('tolerance')->nullable()->after('country_of_origin');
            $table->string('port_of_loading')->nullable()->after('tolerance');
            $table->string('port_of_discharge')->nullable()->after('port_of_loading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_lcs', function (Blueprint $table) {
            $table->dropColumn(['lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge']);
        });
    }
};
