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
        $this->command->info('Step 1: Running NotificationSeeder...');
        $this->call(NotificationSeeder::class);
        
        $this->command->info('Step 2: Running PlansTableSeeder...');
        $this->call(PlansTableSeeder::class);
        
        $this->command->info('Step 3: Running UsersTableSeeder...');
        $this->call(UsersTableSeeder::class);
        
        $this->command->info('Step 4: Running HRMSetupSeeder...');
        $this->call(HRMSetupSeeder::class);
        
        $this->command->info('Step 5: Running LeaveTypesSeeder...');
        $this->call(LeaveTypesSeeder::class);
        
        $this->command->info('Step 6: Running PayrollSetupSeeder...');
        $this->call(PayrollSetupSeeder::class);
        
        $this->command->info('Step 7: Running ContractTypesSeeder...');
        $this->call(ContractTypesSeeder::class);
        
        $this->command->info('Step 8: Running ProductCategoriesSeeder...');
        $this->call(ProductCategoriesSeeder::class);
        
        $this->command->info('Step 9: Running AwardTypesSeeder...');
        $this->call(AwardTypesSeeder::class);
        
        $this->command->info('Step 10: Running PerformanceTypesSeeder...');
        $this->call(PerformanceTypesSeeder::class);
        
        $this->command->info('Step 11: Running GoalTypesSeeder...');
        $this->call(GoalTypesSeeder::class);
        
        $this->command->info('Step 12: Running TrainingTypesSeeder...');
        $this->call(TrainingTypesSeeder::class);
        
        $this->command->info('Step 13: Running TerminationTypesSeeder...');
        $this->call(TerminationTypesSeeder::class);
        
        $this->command->info('Step 14: Running AiTemplateSeeder...');
        $this->call(AiTemplateSeeder::class);
        
        $this->command->info('Step 15: Running Module Migrations (LandingPage)...');
        try {
            Artisan::call('module:migrate LandingPage');
            Artisan::call('module:seed LandingPage');
        } catch (\Exception $e) {
            $this->command->error('Module LandingPage failed: ' . $e->getMessage());
        }

        $this->command->info('Finalizing setup...');
        Utility::languagecreate();
    }
}
