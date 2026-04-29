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
        Schema::table('sales_weight_slips', function (Blueprint $table) {
            $table->dropForeign(['tanker_id']);
            $table->string('tanker_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_weight_slips', function (Blueprint $table) {
            // Reverting to bigint might be complex if there is data, but usually:
            $table->unsignedBigInteger('tanker_id')->change();
            $table->foreign('tanker_id')->references('id')->on('sales_ci_tankers')->onDelete('cascade');
        });
    }
};
