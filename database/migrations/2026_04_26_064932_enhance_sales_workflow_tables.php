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
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->string('hs_code')->nullable()->after('signature');
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->string('client_pi_number')->nullable()->after('pi_number');
            $table->string('seller_name')->nullable()->after('amount');
            $table->text('seller_address')->nullable()->after('seller_name');
            $table->string('seller_mobile')->nullable()->after('seller_address');
            $table->string('seller_email')->nullable()->after('seller_mobile');
            $table->string('buyer_name')->nullable()->after('seller_email');
            $table->text('buyer_address')->nullable()->after('buyer_name');
            $table->string('buyer_mobile')->nullable()->after('buyer_address');
            $table->string('buyer_email')->nullable()->after('buyer_mobile');
        });

        Schema::table('sales_lcs', function (Blueprint $table) {
            $table->string('client_lc_number')->nullable()->after('lc_no');
            $table->string('seller_name')->nullable()->after('lc_validity_date');
            $table->text('seller_address')->nullable()->after('seller_name');
            $table->string('seller_mobile')->nullable()->after('seller_address');
            $table->string('seller_email')->nullable()->after('seller_mobile');
            $table->string('buyer_name')->nullable()->after('seller_email');
            $table->text('buyer_address')->nullable()->after('buyer_name');
            $table->string('buyer_mobile')->nullable()->after('buyer_address');
            $table->string('buyer_email')->nullable()->after('buyer_mobile');
        });

        Schema::table('sales_weight_slips', function (Blueprint $table) {
            $table->string('in_out_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales_pos', function (Blueprint $table) {
            $table->dropColumn('hs_code');
        });

        Schema::table('sales_pis', function (Blueprint $table) {
            $table->dropColumn(['client_pi_number', 'seller_name', 'seller_address', 'seller_mobile', 'seller_email', 'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email']);
        });

        Schema::table('sales_lcs', function (Blueprint $table) {
            $table->dropColumn(['client_lc_number', 'seller_name', 'seller_address', 'seller_mobile', 'seller_email', 'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email']);
        });

        Schema::table('sales_weight_slips', function (Blueprint $table) {
            $table->string('in_out_number')->nullable(false)->change();
        });
    }
};
