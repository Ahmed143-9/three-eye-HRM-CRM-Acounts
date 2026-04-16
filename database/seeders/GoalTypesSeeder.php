<?php

namespace Database\Seeders;

use App\Models\GoalType;
use Illuminate\Database\Seeder;

class GoalTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $goalTypes = [
            ['name' => 'Revenue Goal', 'created_by' => $created_by],
            ['name' => 'Sales Goal', 'created_by' => $created_by],
            ['name' => 'Customer Satisfaction Goal', 'created_by' => $created_by],
            ['name' => 'Project Completion Goal', 'created_by' => $created_by],
            ['name' => 'Quality Improvement Goal', 'created_by' => $created_by],
            ['name' => 'Training Goal', 'created_by' => $created_by],
            ['name' => 'Cost Reduction Goal', 'created_by' => $created_by],
        ];

        foreach ($goalTypes as $type) {
            GoalType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
