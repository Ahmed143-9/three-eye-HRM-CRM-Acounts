<?php

namespace Database\Seeders;

use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        // Create Product/Service Categories
        $categories = [
            ['name' => 'Electronics', 'type' => 'product', 'color' => '#3498db', 'created_by' => $created_by],
            ['name' => 'Furniture', 'type' => 'product', 'color' => '#e74c3c', 'created_by' => $created_by],
            ['name' => 'Office Supplies', 'type' => 'product', 'color' => '#2ecc71', 'created_by' => $created_by],
            ['name' => 'Software', 'type' => 'product', 'color' => '#9b59b6', 'created_by' => $created_by],
            ['name' => 'Consulting', 'type' => 'service', 'color' => '#f39c12', 'created_by' => $created_by],
            ['name' => 'Maintenance', 'type' => 'service', 'color' => '#1abc9c', 'created_by' => $created_by],
            ['name' => 'Training', 'type' => 'service', 'color' => '#34495e', 'created_by' => $created_by],
            ['name' => 'Support', 'type' => 'service', 'color' => '#e67e22', 'created_by' => $created_by],
        ];

        foreach ($categories as $category) {
            ProductServiceCategory::firstOrCreate(
                ['name' => $category['name'], 'created_by' => $category['created_by']],
                $category
            );
        }

        // Create Product/Service Units
        $units = [
            ['name' => 'Piece', 'created_by' => $created_by],
            ['name' => 'Unit', 'created_by' => $created_by],
            ['name' => 'Box', 'created_by' => $created_by],
            ['name' => 'Dozen', 'created_by' => $created_by],
            ['name' => 'Hour', 'created_by' => $created_by],
            ['name' => 'Day', 'created_by' => $created_by],
            ['name' => 'Month', 'created_by' => $created_by],
            ['name' => 'Year', 'created_by' => $created_by],
        ];

        foreach ($units as $unit) {
            ProductServiceUnit::firstOrCreate(
                ['name' => $unit['name'], 'created_by' => $unit['created_by']],
                $unit
            );
        }
    }
}
