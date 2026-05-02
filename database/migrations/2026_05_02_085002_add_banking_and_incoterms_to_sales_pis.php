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
            $table->string('incoterm')->nullable()->after('buyer_email');
            $table->string('bank_name')->nullable()->after('incoterm');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('branch')->nullable()->after('account_name');
            $table->string('account_no')->nullable()->after('branch');
            $table->string('swift_code')->nullable()->after('account_no');
            $table->text('terms_and_conditions')->nullable()->after('swift_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_pis', function (Blueprint $table) {
            $table->dropColumn([
                'incoterm',
                'bank_name',
                'account_name',
                'branch',
                'account_no',
                'swift_code',
                'terms_and_conditions',
            ]);
        });
    }
};
