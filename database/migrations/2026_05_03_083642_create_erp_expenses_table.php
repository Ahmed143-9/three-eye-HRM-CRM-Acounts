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
        Schema::create('erp_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('erp_expense_category_id')->nullable();
            $table->date('date')->nullable();
            $table->string('billing_month')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 16, 2)->default(0.0);
            $table->string('status')->default('Pending Approval');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('transport_id')->nullable();
            $table->string('trip_no')->nullable();
            $table->decimal('net_salary', 16, 2)->default(0.0);
            $table->decimal('deduction_amount', 16, 2)->default(0.0);
            $table->text('cause_of_deduction')->nullable();
            $table->string('attachment')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('hold_reason')->nullable();
            $table->text('send_back_reason')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_status')->default('Unpaid');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('voucher_no')->nullable();
            $table->unsignedBigInteger('accounting_bill_id')->nullable();
            $table->text('accountant_note')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->unsignedBigInteger('erp_salary_sheet_id')->nullable();
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_expenses');
    }
};
