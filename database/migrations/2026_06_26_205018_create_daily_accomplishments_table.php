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
        Schema::create('daily_accomplishments', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->default(0);
            $table->date('date');
            $table->text('summary')->nullable();
            $table->text('challenges')->nullable();
            $table->decimal('hours_spent', 5, 2)->default(0);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_accomplishments');
    }
};
