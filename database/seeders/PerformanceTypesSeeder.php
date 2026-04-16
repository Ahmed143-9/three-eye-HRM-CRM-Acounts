<?php

namespace Database\Seeders;

use App\Models\PerformanceType;
use Illuminate\Database\Seeder;

class PerformanceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $performanceTypes = [
            ['name' => 'Excellent', 'created_by' => $created_by],
            ['name' => 'Very Good', 'created_by' => $created_by],
            ['name' => 'Good', 'created_by' => $created_by],
            ['name' => 'Average', 'created_by' => $created_by],
            ['name' => 'Below Average', 'created_by' => $created_by],
            ['name' => 'Poor', 'created_by' => $created_by],
        ];

        foreach ($performanceTypes as $type) {
            PerformanceType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
