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
        Schema::table('clients', function (Blueprint $table) {
            $table->text('file_attachment')->nullable()->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->text('file_attachment')->nullable()->change();
        });
        Schema::table('consultants', function (Blueprint $table) {
            $table->text('file_attachment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('file_attachment')->nullable()->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('file_attachment')->nullable()->change();
        });
        Schema::table('consultants', function (Blueprint $table) {
            $table->string('file_attachment')->nullable()->change();
        });
    }
};
