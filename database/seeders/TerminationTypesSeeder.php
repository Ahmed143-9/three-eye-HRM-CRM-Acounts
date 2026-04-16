<?php

namespace Database\Seeders;

use App\Models\TerminationType;
use Illuminate\Database\Seeder;

class TerminationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $terminationTypes = [
            ['name' => 'Voluntary Resignation', 'created_by' => $created_by],
            ['name' => 'Involuntary Termination', 'created_by' => $created_by],
            ['name' => 'End of Contract', 'created_by' => $created_by],
            ['name' => 'Retirement', 'created_by' => $created_by],
            ['name' => 'Mutual Agreement', 'created_by' => $created_by],
            ['name' => 'Misconduct', 'created_by' => $created_by],
            ['name' => 'Poor Performance', 'created_by' => $created_by],
        ];

        foreach ($terminationTypes as $type) {
            TerminationType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
