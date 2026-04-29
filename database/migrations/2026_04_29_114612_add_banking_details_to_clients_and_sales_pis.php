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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('delivery_address');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('branch_name')->nullable()->after('account_name');
            $table->string('account_no')->nullable()->after('branch_name');
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('buyer_email');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('branch_name')->nullable()->after('account_name');
            $table->string('account_no')->nullable()->after('branch_name');
            $table->string('swift_code')->nullable()->after('account_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'branch_name', 'account_no']);
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'branch_name', 'account_no', 'swift_code']);
        });
    }
};
