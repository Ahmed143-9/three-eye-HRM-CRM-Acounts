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
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            // Earnings breakdown
            if (!Schema::hasColumn('erp_salary_sheets', 'basic_salary')) {
                $table->decimal('basic_salary', 15, 2)->default(0)->after('net_salary');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'hra')) {
                $table->decimal('hra', 15, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'conveyance_allowance')) {
                $table->decimal('conveyance_allowance', 15, 2)->default(0)->after('hra');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'medical_allowance')) {
                $table->decimal('medical_allowance', 15, 2)->default(0)->after('conveyance_allowance');
            }

            // Deductions
            if (!Schema::hasColumn('erp_salary_sheets', 'pf_contribution')) {
                $table->decimal('pf_contribution', 15, 2)->default(0)->after('medical_allowance');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'professional_tax')) {
                $table->decimal('professional_tax', 15, 2)->default(0)->after('pf_contribution');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'tds')) {
                $table->decimal('tds', 15, 2)->default(0)->after('professional_tax');
            }
            if (!Schema::hasColumn('erp_salary_sheets', 'salary_advance')) {
                $table->decimal('salary_advance', 15, 2)->default(0)->after('tds');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_salary_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'basic_salary',
                'hra',
                'conveyance_allowance',
                'medical_allowance',
                'pf_contribution',
                'professional_tax',
                'tds',
                'salary_advance'
            ]);
        });
    }
};
