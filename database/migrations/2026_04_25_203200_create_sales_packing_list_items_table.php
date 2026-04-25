<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_packing_list_items');
    }
};
