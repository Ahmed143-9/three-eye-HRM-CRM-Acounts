<?php

namespace Database\Seeders;

use App\Models\TrainingType;
use Illuminate\Database\Seeder;

class TrainingTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $trainingTypes = [
            ['name' => 'Technical Training', 'created_by' => $created_by],
            ['name' => 'Soft Skills Training', 'created_by' => $created_by],
            ['name' => 'Leadership Training', 'created_by' => $created_by],
            ['name' => 'Safety Training', 'created_by' => $created_by],
            ['name' => 'Compliance Training', 'created_by' => $created_by],
            ['name' => 'Product Training', 'created_by' => $created_by],
            ['name' => 'Onboarding Training', 'created_by' => $created_by],
        ];

        foreach ($trainingTypes as $type) {
            TrainingType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
