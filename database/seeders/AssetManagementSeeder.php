<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\EmployeeAsset;

class AssetManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user as creator
        $creatorId = 1; // Adjust as needed
        
        // Sample Assets
        $assets = [
            [
                'name' => 'MacBook Pro 16"',
                'category' => 'IT',
                'condition' => 'New',
                'status' => 'Available',
                'purchase_date' => '2025-01-15',
                'amount' => 2499.99,
                'manufacturer' => 'Apple',
                'model_number' => 'MRW13',
                'serial_number' => 'C02XK8WPJHD4',
                'location' => 'IT Storage Room',
                'warranty_until' => '2027-01-15',
                'description' => 'MacBook Pro 16-inch with M3 Max chip, 36GB RAM, 1TB SSD',
                'created_by' => $creatorId,
            ],
            [
                'name' => 'Dell Monitor 27"',
                'category' => 'IT',
                'condition' => 'New',
                'status' => 'Available',
                'purchase_date' => '2025-02-10',
                'amount' => 449.99,
                'manufacturer' => 'Dell',
                'model_number' => 'U2723QE',
                'serial_number' => 'CN0Y4K20',
                'location' => 'IT Storage Room',
                'warranty_until' => '2028-02-10',
                'description' => 'Dell UltraSharp 27 4K USB-C Monitor',
                'created_by' => $creatorId,
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'category' => 'Furniture',
                'condition' => 'New',
                'status' => 'Available',
                'purchase_date' => '2025-03-05',
                'amount' => 599.00,
                'manufacturer' => 'Herman Miller',
                'model_number' => 'Aeron',
                'serial_number' => 'HM2025001',
                'location' => 'Office Floor 2',
                'warranty_until' => '2035-03-05',
                'description' => 'Herman Miller Aeron Ergonomic Office Chair, Size B',
                'created_by' => $creatorId,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'category' => 'Electronics',
                'condition' => 'New',
                'status' => 'Available',
                'purchase_date' => '2025-01-20',
                'amount' => 1199.00,
                'manufacturer' => 'Apple',
                'model_number' => 'A3108',
                'serial_number' => 'F2LXK9WPJHD5',
                'location' => 'IT Storage Room',
                'warranty_until' => '2026-01-20',
                'description' => 'iPhone 15 Pro 256GB Natural Titanium',
                'created_by' => $creatorId,
            ],
            [
                'name' => 'Standing Desk',
                'category' => 'Furniture',
                'condition' => 'New',
                'status' => 'Available',
                'purchase_date' => '2025-02-15',
                'amount' => 799.00,
                'manufacturer' => 'Uplift',
                'model_number' => 'V2',
                'serial_number' => 'UP2025002',
                'location' => 'Office Floor 1',
                'warranty_until' => '2030-02-15',
                'description' => 'Uplift V2 Standing Desk, 60"x30", Bamboo Top',
                'created_by' => $creatorId,
            ],
            [
                'name' => 'Canon Printer',
                'category' => 'Electronics',
                'condition' => 'Used',
                'status' => 'Available',
                'purchase_date' => '2024-06-10',
                'amount' => 349.99,
                'manufacturer' => 'Canon',
                'model_number' => 'imageCLASS MF445dw',
                'serial_number' => 'CN2024003',
                'location' => 'Office Floor 1',
                'warranty_until' => '2025-06-10',
                'description' => 'Canon imageCLASS All-in-One Laser Printer',
                'created_by' => $creatorId,
            ],
        ];

        echo "\n📦 Creating sample assets...\n";
        
        foreach ($assets as $assetData) {
            $asset = Asset::create($assetData);
            echo "✅ Created: {$asset->asset_code} - {$asset->name}\n";
        }

        // Get some employees for assignment
        $employees = Employee::where('created_by', $creatorId)->limit(3)->get();

        if ($employees->count() > 0) {
            echo "\n👥 Assigning assets to employees...\n";
            
            // Assign first asset to first employee
            $assignment1 = EmployeeAsset::create([
                'employee_id' => $employees[0]->id,
                'asset_id' => 1, // MacBook Pro
                'assign_date' => now(),
                'status' => 'Assigned',
                'remarks' => 'Assigned for development work',
                'assigned_by' => $creatorId,
                'created_by' => $creatorId,
            ]);
            
            // Update asset status
            Asset::where('id', 1)->update(['status' => 'Assigned']);
            
            echo "✅ Assigned MacBook Pro to {$employees[0]->name}\n";

            // Assign second asset if we have more employees
            if ($employees->count() > 1) {
                $assignment2 = EmployeeAsset::create([
                    'employee_id' => $employees[1]->id,
                    'asset_id' => 2, // Dell Monitor
                    'assign_date' => now(),
                    'status' => 'Assigned',
                    'remarks' => 'Dual monitor setup',
                    'assigned_by' => $creatorId,
                    'created_by' => $creatorId,
                ]);
                
                Asset::where('id', 2)->update(['status' => 'Assigned']);
                
                echo "✅ Assigned Dell Monitor to {$employees[1]->name}\n";
            }
        } else {
            echo "\n⚠️  No employees found. Please create employees first.\n";
        }

        echo "\n✅ Asset Management seeder completed successfully!\n";
        echo "\n📊 Summary:\n";
        echo "   - Total Assets: " . Asset::where('created_by', $creatorId)->count() . "\n";
        echo "   - Available: " . Asset::where('created_by', $creatorId)->where('status', 'Available')->count() . "\n";
        echo "   - Assigned: " . Asset::where('created_by', $creatorId)->where('status', 'Assigned')->count() . "\n";
        echo "   - Assignments: " . EmployeeAsset::where('created_by', $creatorId)->count() . "\n";
        echo "\n";
    }
}
