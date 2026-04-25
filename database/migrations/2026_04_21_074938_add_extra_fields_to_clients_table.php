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
            $table->string('tin_no')->nullable()->after('phone');
            $table->string('bin_number')->nullable()->after('tin_no');
            $table->string('irc_no')->nullable()->after('bin_number');
            $table->string('contact_person_name')->nullable()->after('irc_no');
            $table->string('contact_person_number')->nullable()->after('contact_person_name');
            $table->string('contact_person_email')->nullable()->after('contact_person_number');
            $table->text('head_office_address')->nullable()->after('contact_person_email');
            $table->text('factory_address')->nullable()->after('head_office_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'tin_no',
                'bin_number',
                'irc_no',
                'contact_person_name',
                'contact_person_number',
                'contact_person_email',
                'head_office_address',
                'factory_address',
            ]);
        });
    }
};
