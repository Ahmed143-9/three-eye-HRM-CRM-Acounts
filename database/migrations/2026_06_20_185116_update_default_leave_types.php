<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LeaveType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Casual Leave to 10 days and Sick Leave to 14 days
        LeaveType::where('title', 'Casual Leave')->update(['days' => 10]);
        LeaveType::where('title', 'Sick Leave')->update(['days' => 14]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        LeaveType::where('title', 'Casual Leave')->update(['days' => 5]);
        LeaveType::where('title', 'Sick Leave')->update(['days' => 10]);
    }
};
