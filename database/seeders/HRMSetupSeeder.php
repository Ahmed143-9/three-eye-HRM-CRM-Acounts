<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;

class HRMSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1; // Super Admin or first company user

        // Create Branches
        $branches = [
            ['name' => 'Head Office', 'created_by' => $created_by],
            ['name' => 'Downtown Branch', 'created_by' => $created_by],
            ['name' => 'Airport Branch', 'created_by' => $created_by],
            ['name' => 'Suburban Branch', 'created_by' => $created_by],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['name' => $branch['name'], 'created_by' => $branch['created_by']],
                $branch
            );
        }

        // Create Departments (linked to branches)
        $departments = [
            ['branch_id' => 1, 'name' => 'Human Resources', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Finance & Accounting', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Information Technology', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Marketing', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Sales', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Operations', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Customer Support', 'created_by' => $created_by],
            ['branch_id' => 1, 'name' => 'Research & Development', 'created_by' => $created_by],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name'], 'created_by' => $department['created_by']],
                $department
            );
        }

        // Create Designations (linked to departments)
        $designations = [
            // HR Department
            ['department_id' => 1, 'name' => 'HR Manager', 'created_by' => $created_by],
            ['department_id' => 1, 'name' => 'HR Executive', 'created_by' => $created_by],
            ['department_id' => 1, 'name' => 'Recruiter', 'created_by' => $created_by],
            
            // Finance Department
            ['department_id' => 2, 'name' => 'Finance Manager', 'created_by' => $created_by],
            ['department_id' => 2, 'name' => 'Senior Accountant', 'created_by' => $created_by],
            ['department_id' => 2, 'name' => 'Accountant', 'created_by' => $created_by],
            ['department_id' => 2, 'name' => 'Financial Analyst', 'created_by' => $created_by],
            
            // IT Department
            ['department_id' => 3, 'name' => 'IT Manager', 'created_by' => $created_by],
            ['department_id' => 3, 'name' => 'Senior Developer', 'created_by' => $created_by],
            ['department_id' => 3, 'name' => 'Developer', 'created_by' => $created_by],
            ['department_id' => 3, 'name' => 'System Administrator', 'created_by' => $created_by],
            ['department_id' => 3, 'name' => 'QA Engineer', 'created_by' => $created_by],
            
            // Marketing Department
            ['department_id' => 4, 'name' => 'Marketing Manager', 'created_by' => $created_by],
            ['department_id' => 4, 'name' => 'Marketing Executive', 'created_by' => $created_by],
            ['department_id' => 4, 'name' => 'Content Writer', 'created_by' => $created_by],
            ['department_id' => 4, 'name' => 'SEO Specialist', 'created_by' => $created_by],
            
            // Sales Department
            ['department_id' => 5, 'name' => 'Sales Manager', 'created_by' => $created_by],
            ['department_id' => 5, 'name' => 'Sales Executive', 'created_by' => $created_by],
            ['department_id' => 5, 'name' => 'Business Development Manager', 'created_by' => $created_by],
            
            // Operations Department
            ['department_id' => 6, 'name' => 'Operations Manager', 'created_by' => $created_by],
            ['department_id' => 6, 'name' => 'Operations Executive', 'created_by' => $created_by],
            ['department_id' => 6, 'name' => 'Administrative Assistant', 'created_by' => $created_by],
            
            // Customer Support
            ['department_id' => 7, 'name' => 'Support Manager', 'created_by' => $created_by],
            ['department_id' => 7, 'name' => 'Support Executive', 'created_by' => $created_by],
            
            // R&D Department
            ['department_id' => 8, 'name' => 'Research Manager', 'created_by' => $created_by],
            ['department_id' => 8, 'name' => 'Research Analyst', 'created_by' => $created_by],
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate(
                ['name' => $designation['name'], 'created_by' => $designation['created_by']],
                $designation
            );
        }
    }
}
