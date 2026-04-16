<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Add new columns for enhanced asset management
            $table->string('asset_code')->unique()->after('id')->nullable();
            $table->enum('category', ['IT', 'Furniture', 'Electronics', 'Vehicles', 'Machinery', 'Other'])->after('name')->default('Other');
            $table->enum('condition', ['New', 'Used', 'Damaged', 'Under Maintenance'])->after('category')->default('New');
            $table->enum('status', ['Available', 'Assigned', 'Lost', 'Maintenance'])->after('condition')->default('Available');
            $table->string('manufacturer')->nullable()->after('description');
            $table->string('model_number')->nullable()->after('manufacturer');
            $table->string('serial_number')->nullable()->after('model_number');
            $table->string('location')->nullable()->after('serial_number');
            $table->string('warranty_until')->nullable()->after('location');
            $table->string('image')->nullable()->after('warranty_until');
            
            // Add indexes for better performance
            $table->index('asset_code');
            $table->index('category');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_code',
                'category',
                'condition',
                'status',
                'manufacturer',
                'model_number',
                'serial_number',
                'location',
                'warranty_until',
                'image'
            ]);
        });
    }
};
