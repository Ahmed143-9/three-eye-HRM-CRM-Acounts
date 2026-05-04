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
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_salary_sheets', 'status')) {
                $table->enum('status', [
                    'Draft', 
                    'Pending Approval', 
                    'Approved', 
                    'Sent To Accounts', 
                    'Processing Payment', 
                    'Paid', 
                    'Rejected'
                ])->default('Draft')->after('serial_no');
            }

            if (!Schema::hasColumn('erp_salary_sheets', 'need_approval_at')) {
                $table->timestamp('need_approval_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('erp_salary_sheets', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('erp_salary_sheets', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('erp_salary_sheets', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            $table->dropColumn(['status', 'need_approval_at', 'approved_at', 'paid_by', 'paid_at']);
        });
    }
};
