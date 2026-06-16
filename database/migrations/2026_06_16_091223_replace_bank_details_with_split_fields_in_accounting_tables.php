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
        $tables = ['clients', 'suppliers', 'consultants'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('bank_details');
                $table->string('bank_name')->nullable()->after('delivery_address');
                $table->string('bank_branch')->nullable()->after('bank_name');
                $table->string('bank_account_number')->nullable()->after('bank_branch');
            });
        }
    }

    public function down(): void
    {
        $tables = ['clients', 'suppliers', 'consultants'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->text('bank_details')->nullable();
                $table->dropColumn(['bank_name', 'bank_branch', 'bank_account_number']);
            });
        }
    }
};
