<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyContactFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emergency_contact_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emergency_contact_id');
            $table->string('file_name');
            $table->string('original_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->foreign('emergency_contact_id')
                  ->references('id')
                  ->on('employee_emergency_contacts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emergency_contact_files');
    }
}
