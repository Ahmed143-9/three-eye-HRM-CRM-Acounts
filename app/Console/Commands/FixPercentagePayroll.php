<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Loan;
use App\Models\SaturationDeduction;
use App\Models\OtherPayment;
use Illuminate\Support\Facades\DB;

class FixPercentagePayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:fix-percentages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes corrupted payroll data where type is percentage but amount is > 100';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $models = [
            Allowance::class,
            Commission::class,
            Loan::class,
            SaturationDeduction::class,
            OtherPayment::class
        ];

        $totalFixed = 0;

        foreach ($models as $model) {
            $count = $model::where('type', 'percentage')->where('amount', '>', 100)->update(['type' => 'fixed']);
            $totalFixed += $count;
            $this->info("Fixed {$count} records in " . class_basename($model));
        }

        $this->info("Successfully converted {$totalFixed} corrupted percentage records to fixed amounts.");
    }
}
