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
        Schema::create('erp_salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month');
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->decimal('deduction_amount', 15, 2)->default(0);
            $table->decimal('final_salary', 15, 2)->default(0);
            $table->string('approval_status')->default('Pending');
            $table->integer('approved_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_salary_sheets');
    }
};
