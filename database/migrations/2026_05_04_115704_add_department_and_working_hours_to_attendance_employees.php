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
        Schema::table('attendance_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_employees', 'department_id')) {
                $table->integer('department_id')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('attendance_employees', 'working_hours')) {
                $table->decimal('working_hours', 8, 2)->default(0)->after('clock_out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn(['department_id', 'working_hours']);
        });
    }
};
