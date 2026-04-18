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
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('to_details');
            $table->string('to_bank_name')->nullable();
            $table->string('to_bank_number')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->text('to_details')->nullable();
            $table->dropColumn(['to_bank_name', 'to_bank_number', 'description']);
        });
    }
};
