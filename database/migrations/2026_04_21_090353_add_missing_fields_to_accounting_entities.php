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
            if (!Schema::hasColumn('clients', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('factory_address');
            }
            if (!Schema::hasColumn('clients', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('billing_address');
            }
            if (!Schema::hasColumn('clients', 'bank_details')) {
                $table->text('bank_details')->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('clients', 'file_attachment')) {
                $table->string('file_attachment')->nullable()->after('bank_details');
            }
        });

        foreach (['suppliers', 'consultants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'billing_address')) {
                    $table->text('billing_address')->nullable()->after('factory_address');
                }
                if (!Schema::hasColumn($tableName, 'bank_details')) {
                    $table->text('bank_details')->nullable()->after('delivery_address');
                }
                if (!Schema::hasColumn($tableName, 'file_attachment')) {
                    $table->string('file_attachment')->nullable()->after('bank_details');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['billing_address', 'delivery_address', 'bank_details', 'file_attachment']);
        });

        foreach (['suppliers', 'consultants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['billing_address', 'bank_details', 'file_attachment']);
            });
        }
    }
};
