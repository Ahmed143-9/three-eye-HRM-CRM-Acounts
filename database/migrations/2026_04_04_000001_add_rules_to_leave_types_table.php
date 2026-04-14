<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRulesToLeaveTypesTable extends Migration
{
    public function up()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->tinyInteger('is_attachment_required')->default(0)->after('days');
            $table->integer('min_advance_days')->default(0)->after('is_attachment_required');
            $table->tinyInteger('is_default')->default(0)->after('min_advance_days');
        });
    }

    public function down()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn(['is_attachment_required', 'min_advance_days', 'is_default']);
        });
    }
}
