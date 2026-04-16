<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        $leaveTypes = [
            ['title' => 'Annual Leave', 'days' => 20, 'created_by' => $created_by],
            ['title' => 'Sick Leave', 'days' => 10, 'created_by' => $created_by],
            ['title' => 'Casual Leave', 'days' => 5, 'created_by' => $created_by],
            ['title' => 'Maternity Leave', 'days' => 90, 'created_by' => $created_by],
            ['title' => 'Paternity Leave', 'days' => 7, 'created_by' => $created_by],
            ['title' => 'Bereavement Leave', 'days' => 3, 'created_by' => $created_by],
            ['title' => 'Marriage Leave', 'days' => 5, 'created_by' => $created_by],
            ['title' => 'Unpaid Leave', 'days' => 0, 'created_by' => $created_by],
            ['title' => 'Compensatory Leave', 'days' => 0, 'created_by' => $created_by],
            ['title' => 'Study Leave', 'days' => 10, 'created_by' => $created_by],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                ['title' => $leaveType['title'], 'created_by' => $leaveType['created_by']],
                $leaveType
            );
        }
    }
}
