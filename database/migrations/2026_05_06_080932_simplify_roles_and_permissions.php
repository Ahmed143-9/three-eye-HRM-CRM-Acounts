<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $newPermissions = [
            // HRM Permissions
            'Manage Employees',
            'Manage Attendance',
            'Manage Payroll',
            'Manage Leaves',
            'Manage Transport',
            'Manage Assets',
            'Manage HR Setup',

            // Accounting Permissions
            'Manage Payables & Receivables',
            'Manage Banking & Billing',
            'Manage Sales Orders',
            'Manage Purchases & Suppliers',
            'Manage Accounting Setup',

            // Expense Management Permissions
            'Submit Expenses',
            'Approve Expenses',

            // Administration Permissions
            'Manage Users',
            'Manage Roles',
            'manage system settings',
        ];

        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->name = $permissionName;
                $permission->save();
            } else {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        // Clear cache to ensure Spatie recognizes new permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Assign all to company role if it exists
        $companyRole = Role::where('name', 'company')->first();
        if ($companyRole) {
            foreach ($newPermissions as $permissionName) {
                try {
                    $companyRole->givePermissionTo($permissionName);
                } catch (\Exception $e) {
                    // Log or ignore if already assigned
                }
            }
        }
    }

    public function down(): void
    {
        // Don't remove in down() automatically to prevent accidental data loss, 
        // but we could if we wanted to be strict.
    }
};
