<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyShipmentDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:shipment-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users when the Latest Shipment Date is exactly 7 days away.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = \Carbon\Carbon::now()->addDays(7)->format('Y-m-d');

        // Check LC
        $lcs = \App\Models\SalesLC::whereDate('latest_shipment_date', $targetDate)->with('order')->get();
        foreach ($lcs as $lc) {
            if ($lc->order) {
                $this->notifyUsers($lc->order, 'LC', $lc->lc_number, $lc->latest_shipment_date);
            }
        }

        // Check CI (If CI has a different shipment date)
        $cis = \App\Models\SalesCI::whereDate('latest_shipment_date', $targetDate)->with('order')->get();
        foreach ($cis as $ci) {
            if ($ci->order) {
                $this->notifyUsers($ci->order, 'CI', $ci->ci_number, $ci->latest_shipment_date);
            }
        }

        $this->info('Shipment date notifications sent successfully.');
    }

    private function notifyUsers($order, $type, $docNumber, $date)
    {
        $message = "Alert: Latest Shipment Date for {$type} {$docNumber} (Order {$order->order_number}) is in 7 days ({$date}).";

        // Notify creator or admins
        $userIds = \App\Models\User::whereIn('type', ['company', 'Admin'])->pluck('id')->toArray();
        if ($order->created_by && !in_array($order->created_by, $userIds)) {
            $userIds[] = $order->created_by;
        }

        foreach ($userIds as $userId) {
            \App\Models\Notification::create([
                'user_id' => $userId,
                'type' => 'shipment_date_warning',
                'title' => 'Shipment Date Warning',
                'message' => $message,
                'related_model' => 'SalesOrder',
                'related_id' => $order->id,
                'created_by' => 0, // System
                'is_read' => 0,
            ]);
        }
    }
}
