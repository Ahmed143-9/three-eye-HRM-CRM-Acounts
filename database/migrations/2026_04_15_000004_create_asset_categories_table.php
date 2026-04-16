<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique(); // e.g., IT, FURN, ELEC
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon class
            $table->string('color')->default('secondary'); // Badge color
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->default(0);
            $table->timestamps();
            
            $table->index('created_by');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
