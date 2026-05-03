<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_expenses', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('erp_expenses', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
            if (!Schema::hasColumn('erp_expenses', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('erp_expenses', 'hold_reason')) {
                $table->text('hold_reason')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('erp_expenses', 'send_back_reason')) {
                $table->text('send_back_reason')->nullable()->after('hold_reason');
            }
            if (!Schema::hasColumn('erp_expenses', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('send_back_reason');
            }
            if (!Schema::hasColumn('erp_expenses', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }
            if (!Schema::hasColumn('erp_expenses', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('erp_expenses', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('erp_expenses', 'voucher_no')) {
                $table->string('voucher_no')->nullable()->after('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            $cols = [
                'rejected_by',
                'rejected_at',
                'rejection_reason',
                'hold_reason',
                'send_back_reason',
                'paid_by',
                'paid_at',
                'payment_method',
                'payment_reference',
                'voucher_no',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('erp_expenses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
