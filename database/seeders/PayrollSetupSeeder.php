<?php

namespace Database\Seeders;

use App\Models\PayslipType;
use App\Models\AllowanceOption;
use App\Models\LoanOption;
use App\Models\DeductionOption;
use Illuminate\Database\Seeder;

class PayrollSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_by = 1;

        // Create Payslip Types
        $payslipTypes = [
            ['name' => 'Monthly', 'created_by' => $created_by],
            ['name' => 'Bi-Weekly', 'created_by' => $created_by],
            ['name' => 'Weekly', 'created_by' => $created_by],
        ];

        foreach ($payslipTypes as $type) {
            PayslipType::firstOrCreate(
                ['name' => $type['name'], 'created_by' => $type['created_by']],
                $type
            );
        }

        // Create Allowance Options
        $allowanceOptions = [
            ['name' => 'House Rent Allowance', 'created_by' => $created_by],
            ['name' => 'Medical Allowance', 'created_by' => $created_by],
            ['name' => 'Transport Allowance', 'created_by' => $created_by],
            ['name' => 'Food Allowance', 'created_by' => $created_by],
            ['name' => 'Phone Allowance', 'created_by' => $created_by],
            ['name' => 'Special Allowance', 'created_by' => $created_by],
        ];

        foreach ($allowanceOptions as $option) {
            AllowanceOption::firstOrCreate(
                ['name' => $option['name'], 'created_by' => $option['created_by']],
                $option
            );
        }

        // Create Loan Options
        $loanOptions = [
            ['name' => 'Personal Loan', 'created_by' => $created_by],
            ['name' => 'Emergency Loan', 'created_by' => $created_by],
            ['name' => 'House Loan', 'created_by' => $created_by],
            ['name' => 'Vehicle Loan', 'created_by' => $created_by],
        ];

        foreach ($loanOptions as $option) {
            LoanOption::firstOrCreate(
                ['name' => $option['name'], 'created_by' => $option['created_by']],
                $option
            );
        }

        // Create Deduction Options
        $deductionOptions = [
            ['name' => 'Tax Deduction', 'created_by' => $created_by],
            ['name' => 'Social Security', 'created_by' => $created_by],
            ['name' => 'Health Insurance', 'created_by' => $created_by],
            ['name' => 'Retirement Fund', 'created_by' => $created_by],
            ['name' => 'Late Deduction', 'created_by' => $created_by],
        ];

        foreach ($deductionOptions as $option) {
            DeductionOption::firstOrCreate(
                ['name' => $option['name'], 'created_by' => $option['created_by']],
                $option
            );
        }
    }
}
