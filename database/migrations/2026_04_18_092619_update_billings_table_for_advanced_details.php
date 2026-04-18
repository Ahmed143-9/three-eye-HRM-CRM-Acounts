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
            $table->dropColumn('banking_details');
            $table->date('from_date')->nullable();
            $table->string('from_bank_name')->nullable();
            $table->string('from_bank_number')->nullable();
            $table->date('to_date')->nullable();
            $table->text('to_details')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('unpaid'); // unpaid, paid
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->text('banking_details')->nullable();
            $table->dropColumn([
                'from_date', 
                'from_bank_name', 
                'from_bank_number', 
                'to_date', 
                'to_details', 
                'attachment',
                'status'
            ]);
        });
    }
};
