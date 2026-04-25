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
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('invoice_number');
            $table->date('date');
            $table->string('billing_direction'); // client, supplier, consultant
            $table->unsignedBigInteger('entity_id');
            $table->text('billing_address')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });

        Schema::create('payable_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payable_id');
            $table->string('serial')->nullable();
            $table->text('order_details')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('payable_id')->references('id')->on('payables')->onDelete('cascade');
        });

        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('invoice_number');
            $table->date('date');
            $table->string('billing_direction'); // client, supplier, consultant
            $table->unsignedBigInteger('entity_id');
            $table->text('billing_address')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });

        Schema::create('receivable_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receivable_id');
            $table->string('serial')->nullable();
            $table->text('order_details')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('receivable_id')->references('id')->on('receivables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivable_items');
        Schema::dropIfExists('receivables');
        Schema::dropIfExists('payable_items');
        Schema::dropIfExists('payables');
    }
};
