<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_user_id');   // Super Admin who receives
            $table->unsignedBigInteger('from_user_id'); // Admin who created the user
            $table->unsignedBigInteger('user_id');      // The new pending user
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_role')->nullable();
            $table->string('created_by')->nullable();   // Admin name
            $table->tinyInteger('is_read')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
