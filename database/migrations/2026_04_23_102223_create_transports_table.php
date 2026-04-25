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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->integer('client_id')->default(0);
            $table->string('manual_client_name')->nullable();
            $table->text('location_address')->nullable();
            $table->string('location_lat')->nullable();
            $table->string('location_lng')->nullable();
            
            $table->string('driver_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('truck_number')->nullable();
            $table->date('starting_date')->nullable();
            $table->text('item_description')->nullable();
            $table->date('delivery_date')->nullable();
            
            $table->integer('payable_id')->default(0);
            $table->string('status')->default('pending');
            $table->integer('created_by')->default(0);
            $table->integer('workspace')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
