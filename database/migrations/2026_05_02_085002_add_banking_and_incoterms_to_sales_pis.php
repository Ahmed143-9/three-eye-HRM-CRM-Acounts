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
        Schema::table('sales_pis', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_pis', 'incoterm')) {
                $table->string('incoterm')->nullable()->after('buyer_email');
            }
            if (!Schema::hasColumn('sales_pis', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('incoterm');
            }
            if (!Schema::hasColumn('sales_pis', 'account_name')) {
                $table->string('account_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('sales_pis', 'branch')) {
                $table->string('branch')->nullable()->after('account_name');
            }
            if (!Schema::hasColumn('sales_pis', 'account_no')) {
                $table->string('account_no')->nullable()->after('branch');
            }
            if (!Schema::hasColumn('sales_pis', 'swift_code')) {
                $table->string('swift_code')->nullable()->after('account_no');
            }
            if (!Schema::hasColumn('sales_pis', 'terms_and_conditions')) {
                $table->text('terms_and_conditions')->nullable()->after('swift_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_pis', function (Blueprint $table) {
            $columns = [
                'incoterm',
                'bank_name',
                'account_name',
                'branch',
                'account_no',
                'swift_code',
                'terms_and_conditions',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('sales_pis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
