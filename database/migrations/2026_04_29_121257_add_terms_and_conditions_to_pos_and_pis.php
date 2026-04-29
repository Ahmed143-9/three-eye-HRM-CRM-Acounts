<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->text('terms_and_conditions')->nullable();
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->text('terms_and_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->dropColumn('terms_and_conditions');
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->dropColumn('terms_and_conditions');
        });
    }
};
