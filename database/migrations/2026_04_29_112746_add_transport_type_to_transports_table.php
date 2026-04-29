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
        Schema::table('transports', function (Blueprint $table) {
            $table->string('transport_type')->nullable();
            $table->decimal('required_trucks', 8, 2)->default(0);
            $table->text('drivers_data')->nullable(); // JSON data for multiple drivers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn(['transport_type', 'required_trucks', 'drivers_data']);
        });
    }
};
