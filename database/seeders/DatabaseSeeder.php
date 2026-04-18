<?php

namespace Database\Seeders;

use App\Models\Utility;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Step 1: Notification and Email Templates
        $this->call(NotificationSeeder::class);
        
        // Step 2: Module Migrations (LandingPage)
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');

        if(!file_exists(storage_path() . "/installed"))
        {
            // Step 3: Plans (required for user creation)
            $this->call(PlansTableSeeder::class);
            
            // Step 4: Users (includes permissions, roles, and super admin)
            $this->call(UsersTableSeeder::class);
            
            // Step 5: HRM Setup - Branches, Departments, Designations
            $this->call(HRMSetupSeeder::class);
            
            // Step 6: Leave Types
            $this->call(LeaveTypesSeeder::class);
            
            // Step 7: Payroll Setup - Payslip types, allowances, loans, deductions
            $this->call(PayrollSetupSeeder::class);
            
            // Step 8: Contract Types
            $this->call(ContractTypesSeeder::class);
            
            // Step 9: Product/Service Categories and Units
            $this->call(ProductCategoriesSeeder::class);
            
            // Step 10: Award Types
            $this->call(AwardTypesSeeder::class);
            
            // Step 11: Performance Types
            $this->call(PerformanceTypesSeeder::class);
            
            // Step 12: Goal Types
            $this->call(GoalTypesSeeder::class);
            
            // Step 13: Training Types
            $this->call(TrainingTypesSeeder::class);
            
            // Step 14: Termination Types
            $this->call(TerminationTypesSeeder::class);
            
            // Step 15: AI Templates
            $this->call(AiTemplateSeeder::class);
            
            // Step 16: Asset Management (DISABLED - Optional)
            // $this->call(AssetManagementSeeder::class);

        }else{
            Utility::languagecreate();

        }
    }
}
