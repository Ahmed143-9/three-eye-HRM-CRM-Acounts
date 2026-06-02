<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class FixEmployeeServer extends Command
{
    protected $signature = 'fix:server';
    protected $description = 'Fixes the missing tables and orphaned users on the server.';

    public function handle()
    {
        $this->info('Starting server fixes...');

        // 1. Delete Orphaned User
        $deleted = User::where('email', 'Sales02@threeeyebd.com')->delete();
        if ($deleted) {
            $this->info('Successfully deleted orphaned user: Sales02@threeeyebd.com');
        } else {
            $this->info('User Sales02@threeeyebd.com not found or already deleted.');
        }

        // 2. Fix Employee Emergency Contacts Table
        if (!Schema::hasTable('employee_emergency_contacts')) {
            $this->warn('Table employee_emergency_contacts is missing. Fixing migration...');
            
            // Delete from migrations table to allow fresh migrate
            DB::table('migrations')->where('migration', 'like', '%employee_emergency_contacts%')->delete();
            DB::table('migrations')->where('migration', 'like', '%emergency_contact_files%')->delete();
            
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_04_01_000000_create_employee_emergency_contacts_table.php',
                '--force' => true
            ]);
            $this->info('Migrated employee_emergency_contacts table.');
            
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_04_01_100000_create_emergency_contact_files_table.php',
                '--force' => true
            ]);
            $this->info('Migrated emergency_contact_files table.');
        } else {
            $this->info('Table employee_emergency_contacts already exists.');
        }

        $this->info('All server fixes completed successfully!');
        return 0;
    }
}
