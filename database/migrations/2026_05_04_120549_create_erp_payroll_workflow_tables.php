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
        // 1. Payroll Batches (Master Sheet)
        if (!Schema::hasTable('erp_payroll_batches')) {
            Schema::create('erp_payroll_batches', function (Blueprint $table) {
                $table->id();
                $table->string('batch_no')->unique();
                $table->string('month'); // YYYY-MM
                $table->integer('department_id')->nullable();
                $table->string('status')->default('Draft'); // Draft, Pending Approval, Approved, Paid
                $table->decimal('total_net_payable', 15, 2)->default(0);
                $table->integer('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->integer('created_by');
                $table->timestamps();
            });
        }

        // 2. Update Salary Sheets (Rows) to link to Batches and add breakdown fields if missing
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_salary_sheets', 'batch_id')) {
                $table->integer('batch_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'department_id')) {
                $table->integer('department_id')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'designation_id')) {
                $table->integer('designation_id')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'basic_salary')) {
                $table->decimal('basic_salary', 15, 2)->default(0);
                $table->decimal('hra', 15, 2)->default(0);
                $table->decimal('conveyance_allowance', 15, 2)->default(0);
                $table->decimal('medical_allowance', 15, 2)->default(0);
            }
        });

        // 3. Payroll Ledgers (Payment History)
        if (!Schema::hasTable('erp_payroll_ledgers')) {
            Schema::create('erp_payroll_ledgers', function (Blueprint $table) {
                $table->id();
                $table->integer('batch_id');
                $table->integer('salary_sheet_id')->nullable(); // For individual payments
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->string('payment_method')->nullable();
                $table->string('transaction_id')->nullable();
                $table->integer('created_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_payroll_batches');
        Schema::dropIfExists('erp_payroll_ledgers');
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            $table->dropColumn(['batch_id', 'department_id', 'designation_id']);
        });
    }
};
