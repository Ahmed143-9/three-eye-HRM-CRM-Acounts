<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ErpExpense;
use App\Models\ErpSalarySheet;
use App\Models\Transport;
use App\Models\SalesOrder;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'related_model',
        'related_id',
        'is_read',
        'created_by',
    ];

    public function toHtml()
    {
        $link       = route('notifications.readAndRedirect', $this->id);
        $icon       = 'ti ti-bell';
        $icon_color = 'bg-primary';
        $text       = '';
        $subtext    = '';

        if ($this->related_model == 'ErpExpense') {
            $expense = \Cache::remember("noti_expense_{$this->related_id}", 3600, function() {
                return ErpExpense::with('employee')->find($this->related_id);
            });
            if ($expense) {
                $icon = 'ti ti-report-money';
                $empName = optional($expense->employee)->name ?? '-';
                $amount = optional(\Auth::user())->priceFormat($expense->amount) ?? $expense->amount;
                $text = "<b>" . __($this->title) . "</b>";
                $subtext = __('Type') . ": " . ucfirst($expense->type) . " | " .
                           __('Emp') . ": " . $empName . " | " .
                           __('Amount') . ": " . $amount;
            }
        } elseif ($this->related_model == 'Transport') {
            $transport = \Cache::remember("noti_transport_{$this->related_id}", 3600, function() {
                return Transport::with('client')->find($this->related_id);
            });
            if ($transport) {
                $icon = 'ti ti-truck-delivery';
                $text = "<b>" . __($this->title) . "</b>";
                $subtext = __('ID') . ": " . $transport->unique_id . " | " .
                           __('Truck') . ": " . ($transport->truck_number ?? '-') . " | " .
                           __('Client') . ": " . ($transport->manual_client_name ?: (optional($transport->client)->name ?? '-'));
            }
        } elseif ($this->related_model == 'SalesOrder') {
            $order = \Cache::remember("noti_order_{$this->related_id}", 3600, function() {
                return SalesOrder::with('customer')->find($this->related_id);
            });
            if ($order) {
                $icon = 'ti ti-shopping-cart';
                $text = "<b>" . __($this->title) . "</b>";
                $subtext = __('Order') . ": " . $order->order_number . " | " .
                           __('Customer') . ": " . (optional($order->customer)->name ?? '-');
            }
        } else {
            $text = "<b>" . __($this->title) . "</b>";
            $subtext = __($this->message);
        }

        // Color mapping
        $typeColors = [
            'expense_submitted' => 'bg-warning',
            'expense_approved' => 'bg-success',
            'expense_rejected' => 'bg-danger',
            'expense_on_hold' => 'bg-secondary',
            'expense_sent_back' => 'bg-warning',
            'expense_payment_ready' => 'bg-info',
            'expense_paid' => 'bg-success',
            'salary_sheet_submitted' => 'bg-warning',
            'salary_approved' => 'bg-success',
            'salary_rejected' => 'bg-danger',
            'transport_created' => 'bg-info',
            'sales_order_finalized' => 'bg-primary',
        ];
        $icon_color = $typeColors[$this->type] ?? 'bg-primary';

        $unreadClass = $this->is_read ? '' : 'notification-unread';
        $dot = $this->is_read ? '' : '<span class="notification-dot"></span>';
        $date = $this->created_at->diffForHumans();

        return '<div class="list-group-item list-group-item-action border-0 mb-1 rounded notification-item ' . $unreadClass . '">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon-container me-3 mt-1">
                            <span class="avatar avatar-sm ' . $icon_color . ' text-white rounded-circle">
                                <i class="' . $icon . ' fs-6"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-sm">' . $text . $dot . '</div>
                                <small class="text-muted text-xs">' . $date . '</small>
                            </div>
                            <div class="text-xs text-muted mt-1">' . $subtext . '</div>
                            <div class="d-flex justify-content-end mt-2">
                                <a href="' . $link . '" class="noti-view-btn text-primary border border-primary px-2 py-1">' . __('View Details') . '</a>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
