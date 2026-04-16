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
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->date('assign_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['Assigned', 'Returned', 'Lost', 'Damaged'])->default('Assigned');
            $table->text('remarks')->nullable();
            $table->string('document')->nullable(); // For upload documents
            $table->integer('assigned_by')->default(0); // User who assigned
            $table->integer('created_by')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('employee_id');
            $table->index('asset_id');
            $table->index('status');
            $table->index('assign_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};
