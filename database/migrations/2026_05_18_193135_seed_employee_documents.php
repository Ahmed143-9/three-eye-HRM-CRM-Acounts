<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $documents = [
            ['name' => 'Resume', 'is_required' => 1, 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NID', 'is_required' => 1, 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Offer Letter', 'is_required' => 0, 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Experience Certificate', 'is_required' => 0, 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Academic Certificates', 'is_required' => 0, 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('documents')->insert($documents);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('documents')->whereIn('name', ['Resume', 'NID', 'Offer Letter', 'Experience Certificate', 'Academic Certificates'])->delete();
    }
};
