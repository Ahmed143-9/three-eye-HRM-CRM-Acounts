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
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'quantity')) {
                $table->integer('quantity')->default(1)->after('description');
            }
        });

        Schema::table('employee_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_assets', 'asset_name')) {
                $table->string('asset_name')->nullable()->after('asset_id');
            }
            if (!Schema::hasColumn('employee_assets', 'image')) {
                $table->string('image')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('employee_assets', 'description')) {
                $table->text('description')->nullable()->after('asset_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('employee_assets', function (Blueprint $table) {
            $table->dropColumn(['asset_name', 'image', 'description']);
        });
    }
};
