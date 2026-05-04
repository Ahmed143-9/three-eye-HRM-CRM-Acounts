<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_performances')) {
            Schema::create('employee_performances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->string('performance_month'); // YYYY-MM
                $table->unsignedBigInteger('department_id')->nullable();
                $table->unsignedBigInteger('designation_id')->nullable();
                $table->integer('present_days')->default(0);
                $table->integer('absent_days')->default(0);
                $table->integer('late_count')->default(0);
                $table->integer('leave_count')->default(0);
                $table->decimal('total_working_hours', 8, 2)->default(0);
                $table->decimal('overtime_hours', 8, 2)->default(0);
                $table->decimal('payable_amount', 15, 2)->default(0);
                $table->decimal('receivable_amount', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('workspace_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'performance_month']);
                $table->index('performance_month');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_performances');
    }
};
