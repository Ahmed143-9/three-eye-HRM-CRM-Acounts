<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonnelFieldsToSalesPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_p_o_s', function (Blueprint $table) {
            $table->string('prepared_by')->nullable()->after('status');
            $table->string('issued_by')->nullable()->after('prepared_by');
            $table->string('acknowledged_by')->nullable()->after('issued_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_p_o_s', function (Blueprint $table) {
            $table->dropColumn(['prepared_by', 'issued_by', 'acknowledged_by']);
        });
    }
}
