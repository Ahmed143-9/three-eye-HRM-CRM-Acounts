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
        Schema::table('erp_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_expenses', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('erp_expenses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('erp_expenses', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('payment_status');
            }
            if (!Schema::hasColumn('erp_expenses', 'erp_salary_sheet_id')) {
                $table->unsignedBigInteger('erp_salary_sheet_id')->nullable()->after('trip_no');
            }
        });

        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_salary_sheets', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('approval_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            $table->dropColumn(['workspace_id', 'approved_at', 'is_paid', 'erp_salary_sheet_id']);
        });
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            $table->dropColumn(['workspace_id']);
        });
    }
};
