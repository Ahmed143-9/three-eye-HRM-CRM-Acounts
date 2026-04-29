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
        Schema::table('sales_lcs', function (Blueprint $table) {
            // Rename existing columns
            $table->renameColumn('lc_no', 'lc_reference_no');
            $table->renameColumn('client_lc_number', 'client_lc_no');
            $table->renameColumn('lc_date', 'date_of_issue');
            $table->renameColumn('amount', 'lc_qty');
            
            // Add new columns
            $table->string('unit')->nullable()->after('lc_qty');
            $table->string('lc_type')->nullable()->after('unit');
            $table->string('incoterm')->nullable()->after('lc_type');
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
        Schema::table('sales_lcs', function (Blueprint $table) {
            $table->renameColumn('lc_reference_no', 'lc_no');
            $table->renameColumn('client_lc_no', 'client_lc_number');
            $table->renameColumn('date_of_issue', 'lc_date');
            $table->renameColumn('lc_qty', 'amount');
            
            $table->dropColumn(['unit', 'lc_type', 'incoterm', 'terms_and_conditions']);
        });
    }
};
