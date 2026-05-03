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
            if (!Schema::hasColumn('erp_expenses', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('erp_expenses', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('erp_expenses', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('designation_id');
            }
            if (!Schema::hasColumn('erp_expenses', 'trip_no')) {
                $table->string('trip_no')->nullable()->after('transport_id');
            }
            if (!Schema::hasColumn('erp_expenses', 'payment_status')) {
                $table->string('payment_status')->default('Unpaid')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_expenses', function (Blueprint $table) {
            //
        });
    }
};
