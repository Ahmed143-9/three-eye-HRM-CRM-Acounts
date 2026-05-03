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
        if (Schema::hasTable('erp_expense_units')) {
            Schema::table('erp_expense_units', function (Blueprint $table) {
                if (!Schema::hasColumn('erp_expense_units', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('erp_expense_units', 'status')) {
                    $table->integer('status')->default(1)->after('created_by');
                }
            });
        } else {
            Schema::create('erp_expense_units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('erp_expense_items')) {
            Schema::table('erp_expense_items', function (Blueprint $table) {
                if (!Schema::hasColumn('erp_expense_items', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable()->after('quantity');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
