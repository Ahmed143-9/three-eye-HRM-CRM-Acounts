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
        $categories = [
            ['name' => 'Stationary', 'module_type' => 'purchase', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Entertainment', 'module_type' => 'purchase', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MISC', 'module_type' => 'purchase', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('erp_expense_categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('erp_expense_categories')->whereIn('name', ['Stationary', 'Entertainment', 'MISC'])->delete();
    }
}
