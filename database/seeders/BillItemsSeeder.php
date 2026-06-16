<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = ['Stationary', 'MISC', 'Entertainment'];
        $userId = 2; // Assuming created_by = 2 for company, but we can do it for all companies
        
        $companies = \App\Models\User::where('type', 'company')->get();
        
        foreach ($companies as $company) {
            foreach ($items as $item) {
                \App\Models\ProductService::firstOrCreate(
                    ['name' => $item, 'created_by' => $company->id],
                    [
                        'sku' => strtoupper(substr($item, 0, 3)) . '-' . rand(1000, 9999),
                        'sale_price' => 0,
                        'purchase_price' => 0,
                        'tax_id' => '',
                        'category_id' => 0,
                        'unit_id' => 0,
                        'type' => 'product',
                        'sale_chartaccount_id' => 0,
                        'expense_chartaccount_id' => 0,
                    ]
                );
            }
        }
    }
}
