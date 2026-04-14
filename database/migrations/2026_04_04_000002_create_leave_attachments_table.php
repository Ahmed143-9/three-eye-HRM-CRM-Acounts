<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('leave_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_id');
            $table->integer('employee_id');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->integer('created_by');
            $table->timestamps();

            $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_attachments');
    }
}
