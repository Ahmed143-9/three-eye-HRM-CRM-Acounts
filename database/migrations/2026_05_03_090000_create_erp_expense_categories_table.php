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
        if (!Schema::hasTable('erp_expense_categories')) {
            Schema::create('erp_expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('module_type', ['purchase', 'convenience', 'utility', 'salary']);
                $table->boolean('is_active')->default(true);
                $table->integer('workspace_id')->nullable();
                $table->integer('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_expense_categories');
    }
};
