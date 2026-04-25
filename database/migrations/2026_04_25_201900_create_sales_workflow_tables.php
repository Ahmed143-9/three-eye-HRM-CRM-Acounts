<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sales_orders')) {
            Schema::create('sales_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->unsignedBigInteger('customer_id');
                $table->string('current_step')->default('PO');
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_pos')) {
            Schema::create('sales_pos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('client_name');
                $table->text('client_address')->nullable();
                $table->string('client_email')->nullable();
                $table->string('client_phone')->nullable();
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->string('signature')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_po_items')) {
            Schema::create('sales_po_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('po_id');
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->string('unit')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->timestamps();
                $table->foreign('po_id')->references('id')->on('sales_pos')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_pis')) {
            Schema::create('sales_pis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('pi_number');
                $table->date('pi_date');
                $table->string('validity')->nullable();
                $table->string('lifting_time')->nullable();
                $table->text('payment_terms')->nullable();
                $table->string('hs_code')->nullable();
                $table->string('country_of_origin')->nullable();
                $table->string('tolerance')->nullable();
                $table->string('port_of_loading')->nullable();
                $table->string('port_of_discharge')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_lcs')) {
            Schema::create('sales_lcs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('pi_id');
                $table->string('lc_no');
                $table->decimal('amount', 15, 2)->default(0);
                $table->date('lc_date');
                $table->date('latest_shipment_date')->nullable();
                $table->date('lc_validity_date')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
                $table->foreign('pi_id')->references('id')->on('sales_pis')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_cis')) {
            Schema::create('sales_cis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('pi_id');
                $table->unsignedBigInteger('lc_id');
                $table->string('ci_number')->nullable();
                $table->date('ci_date');
                $table->date('lc_validity_date')->nullable();
                $table->date('latest_shipment_date')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
                $table->foreign('pi_id')->references('id')->on('sales_pis')->onDelete('cascade');
                $table->foreign('lc_id')->references('id')->on('sales_lcs')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_packing_lists')) {
            Schema::create('sales_packing_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('file_path')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_packing_list_items')) {
            Schema::create('sales_packing_list_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('packing_list_id');
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->string('unit')->nullable();
                $table->timestamps();
                $table->foreign('packing_list_id')->references('id')->on('sales_packing_lists')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_ci_tankers')) {
            Schema::create('sales_ci_tankers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ci_id');
                $table->string('tanker_number');
                $table->decimal('quantity_mt', 15, 3)->default(0);
                $table->decimal('cpt_usd', 15, 2)->default(0);
                $table->decimal('total_amount_usd', 15, 2)->default(0);
                $table->timestamps();
                $table->foreign('ci_id')->references('id')->on('sales_cis')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_consignment_notes')) {
            Schema::create('sales_consignment_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('file_path')->nullable();
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_weight_slips')) {
            Schema::create('sales_weight_slips', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('consignment_note_id');
                $table->unsignedBigInteger('tanker_id');
                $table->string('in_out_number');
                $table->decimal('gross_weight', 15, 3)->default(0);
                $table->decimal('tare_weight', 15, 3)->default(0);
                $table->decimal('net_weight', 15, 3)->default(0);
                $table->timestamps();
                $table->foreign('consignment_note_id')->references('id')->on('sales_consignment_notes')->onDelete('cascade');
                $table->foreign('tanker_id')->references('id')->on('sales_ci_tankers')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_weight_slips');
        Schema::dropIfExists('sales_consignment_notes');
        Schema::dropIfExists('sales_ci_tankers');
        Schema::dropIfExists('sales_packing_lists');
        Schema::dropIfExists('sales_cis');
        Schema::dropIfExists('sales_lcs');
        Schema::dropIfExists('sales_pis');
        Schema::dropIfExists('sales_po_items');
        Schema::dropIfExists('sales_pos');
        Schema::dropIfExists('sales_orders');
    }
};
