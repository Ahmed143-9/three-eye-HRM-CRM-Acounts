<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;

class ContractTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $contractTypes = [
            ['name' => 'Permanent', 'created_by' => $created_by],
            ['name' => 'Temporary', 'created_by' => $created_by],
            ['name' => 'Full-Time', 'created_by' => $created_by],
            ['name' => 'Part-Time', 'created_by' => $created_by],
            ['name' => 'Contract', 'created_by' => $created_by],
            ['name' => 'Freelance', 'created_by' => $created_by],
            ['name' => 'Internship', 'created_by' => $created_by],
        ];

        foreach ($contractTypes as $type) {
            ContractType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }
    }
}
