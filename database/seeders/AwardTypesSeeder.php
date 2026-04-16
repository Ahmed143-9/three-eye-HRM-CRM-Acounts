<?php

namespace Database\Seeders;

use App\Models\AwardType;
use Illuminate\Database\Seeder;

class AwardTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $awardTypes = [
            ['name' => 'Employee of the Month', 'created_by' => $created_by],
            ['name' => 'Employee of the Year', 'created_by' => $created_by],
            ['name' => 'Best Performance', 'created_by' => $created_by],
            ['name' => 'Best Team Player', 'created_by' => $created_by],
            ['name' => 'Innovation Award', 'created_by' => $created_by],
            ['name' => 'Leadership Award', 'created_by' => $created_by],
            ['name' => 'Customer Service Excellence', 'created_by' => $created_by],
            ['name' => 'Safety Award', 'created_by' => $created_by],
        ];

        foreach ($awardTypes as $type) {
            AwardType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
