<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_deliveries', 'inventory_item_id')) {
                $table->unsignedBigInteger('inventory_item_id')->nullable()->after('required_units');
            }
        });
    }

    public function down()
    {
        Schema::table('sales_deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('sales_deliveries', 'inventory_item_id')) {
                $table->dropColumn('inventory_item_id');
            }
        });
    }
};
