<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_expenses', 'accounting_bill_id')) {
                $table->unsignedBigInteger('accounting_bill_id')->nullable()->after('approved_at')
                      ->comment('Linked Bill ID in accounting module after admin approval');
            }
            if (!Schema::hasColumn('erp_expenses', 'accountant_note')) {
                $table->text('accountant_note')->nullable()->after('accounting_bill_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            $table->dropColumn(['accounting_bill_id', 'accountant_note']);
        });
    }
};
