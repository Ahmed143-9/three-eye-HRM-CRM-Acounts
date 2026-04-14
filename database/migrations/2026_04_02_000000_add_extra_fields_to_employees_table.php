<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('address');
            $table->string('facebook')->nullable()->after('tax_payer_id');
            $table->string('linkedin')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('linkedin');
            $table->string('instagram')->nullable()->after('twitter');
            $table->integer('probation_period')->nullable()->after('instagram')->comment('In days');
            $table->integer('notice_period')->nullable()->after('probation_period')->comment('In days');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image',
                'facebook',
                'linkedin',
                'twitter',
                'instagram',
                'probation_period',
                'notice_period',
            ]);
        });
    }
}
