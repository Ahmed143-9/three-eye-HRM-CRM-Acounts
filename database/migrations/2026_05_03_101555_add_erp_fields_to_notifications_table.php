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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->text('message')->nullable()->after('title');
            $table->string('related_model')->nullable()->after('message');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_model');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'message', 'related_model', 'related_id', 'created_by']);
        });
    }
};
