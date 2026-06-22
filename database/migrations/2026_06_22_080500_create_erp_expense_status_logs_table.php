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
        if (!Schema::hasTable('erp_expense_status_logs')) {
            Schema::create('erp_expense_status_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('erp_expense_id');
                $table->string('status');
                $table->text('comments')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->foreign('erp_expense_id')->references('id')->on('erp_expenses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_expense_status_logs');
    }
};
