<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'manage expense',
            'create expense',
            'edit expense',
            'delete expense',
            'show expense',
            'approve expense',
            'view office expense history',
        ];

        foreach ($permissions as $permission) {
            if (Permission::where('name', $permission)->count() == 0) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        $companyRole = Role::where('name', 'company')->first();
        if ($companyRole) {
            foreach ($permissions as $permission) {
                $companyRole->givePermissionTo($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
