<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_salary_sheets', 'serial_no')) {
                $table->string('serial_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'present_days')) {
                $table->integer('present_days')->default(0)->after('designation_id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'absent_days')) {
                $table->integer('absent_days')->default(0)->after('present_days');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'late_count')) {
                $table->integer('late_count')->default(0)->after('absent_days');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'leave_count')) {
                $table->integer('leave_count')->default(0)->after('late_count');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'working_hours')) {
                $table->decimal('working_hours', 8, 2)->default(0)->after('leave_count');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0)->after('working_hours');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'payable_amount')) {
                $table->decimal('payable_amount', 15, 2)->default(0)->after('overtime_hours');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'receivable_amount')) {
                $table->decimal('receivable_amount', 15, 2)->default(0)->after('payable_amount');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'cause_of_deduction')) {
                $table->text('cause_of_deduction')->nullable()->after('deduction_amount');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'payment_status')) {
                $table->string('payment_status')->default('Unpaid')->after('approval_status');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'remarks')) {
                $table->text('remarks')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'serial_no', 'department_id', 'designation_id',
                'present_days', 'absent_days', 'late_count', 'leave_count',
                'working_hours', 'overtime_hours', 'payable_amount', 'receivable_amount',
                'cause_of_deduction', 'payment_status', 'remarks',
            ]);
        });
    }
};
