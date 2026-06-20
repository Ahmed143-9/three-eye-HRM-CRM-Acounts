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
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('final_destination')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('packing')->nullable();
            $table->string('transport_mode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->dropColumn([
                'port_of_loading',
                'port_of_discharge',
                'final_destination',
                'country_of_origin',
                'packing',
                'transport_mode'
            ]);
        });
    }
};
