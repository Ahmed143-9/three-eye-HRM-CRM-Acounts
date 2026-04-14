<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLateUpdateTrackingToAttendanceEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            // Flag: was this record created/updated after the attendance date?
            $table->boolean('is_late_update')->default(false)->after('total_rest');
            // Count of how many times this record was updated after the attendance date
            $table->unsignedInteger('late_update_count')->default(0)->after('is_late_update');
        });
    }

    public function down()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn(['is_late_update', 'late_update_count']);
        });
    }
}
