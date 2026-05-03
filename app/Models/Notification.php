<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ErpExpense;
use App\Models\ErpSalarySheet;

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
        $data       = json_decode($this->data);
        $link       = '#';
        $icon       = 'ti ti-bell';
        $icon_color = 'bg-primary';
        $text       = '';

        if (!empty($this->title)) {
            $text = "<b>" . __($this->title) . "</b><br>" . __($this->message);
            
            // Determine link based on related_model
            $link = route('notifications.readAndRedirect', $this->id);
            if ($this->related_model == 'ErpExpense') {
                $expense = ErpExpense::find($this->related_id);
                if ($expense) {
                    $icon = 'ti ti-report-money';
                    $text = "<b>" . __($this->title) . "</b><br>" . 
                            __('Type') . ": " . ucfirst($expense->type) . "<br>" .
                            __('Emp') . ": " . ($expense->employee->name ?? '-') . "<br>" .
                            __('Amount') . ": " . \Auth::user()->priceFormat($expense->amount);
                }
            } elseif ($this->related_model == 'ErpSalarySheet') {
                $icon = 'ti ti-cash';
            }

            if ($this->type == 'expense_submitted') $icon_color = 'bg-warning';
            elseif ($this->type == 'expense_approved') $icon_color = 'bg-success';
            elseif ($this->type == 'expense_rejected') $icon_color = 'bg-danger';

            $date = $this->created_at->diffForHumans();
            return '<div class="list-group-item list-group-item-action border-0 mb-1 rounded ' . ($this->is_read ? '' : 'bg-light-primary') . '">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-2 mt-1">
                                <span class="avatar avatar-sm ' . $icon_color . ' text-white rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="' . $icon . ' fs-6"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="text-sm lh-140">' . $text . '</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted text-xs">' . $date . '</small>
                                    <a href="' . $link . '" class="text-xs text-primary font-weight-bold">' . __('View') . '</a>
                                </div>
                            </div>
                        </div>
                    </div>';
        }

        if(isset($data->updated_by) && !empty($data->updated_by))
        {
            $usr = User::find($data->updated_by);
        }

        if(!empty($usr))
        {
            // For Deals Notification
            if($this->type == 'assign_deal')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Added you') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-plus";
                $icon_color = 'bg-primary';
            }

            if($this->type == 'create_deal_call')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Create new Deal Call') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-phone";
                $icon_color = 'bg-info';
            }

            if($this->type == 'update_deal_source')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Update Sources') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-file-alt";
                $icon_color = 'bg-warning';
            }

            if($this->type == 'create_task')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Create new Task') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-tasks";
                $icon_color = 'bg-primary';
            }

            if($this->type == 'add_product')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Add new Products') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-dolly";
                $icon_color = 'bg-danger';
            }

            if($this->type == 'add_discussion')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Add new Discussion') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-comments";
                $icon_color = 'bg-info';
            }

            if($this->type == 'move_deal')
            {
                $link       = isset($data->deal_id) ? route('deals.show', [$data->deal_id]) : '#';
                $text       = $usr->name . " " . __('Moved the deal') . " <b class='font-weight-bold'>" . $data->name . "</b> " . __('from') . " " . __(ucwords($data->old_status)) . " " . __('to') . " " . __(ucwords($data->new_status));
                $icon       = "fa fa-arrows-alt";
                $icon_color = 'bg-primary';
            }
            // end deals

            // for estimations
            if($this->type == 'assign_estimation')
            {
                $link       = isset($data->estimation_id) ? route('estimations.show', [$data->estimation_id]) : '#';
                $text       = $usr->name . " " . __('Added you') . " " . __('in estimation') . " <b class='font-weight-bold'>" . $data->estimation_name . "</b> ";
                $icon       = "fa fa-plus";
                $icon_color = 'bg-primary';
            }
            // end estimations

            // For Leads Notification
            if($this->type == 'assign_lead')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Added you') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-plus";
                $icon_color = 'bg-primary';
            }

            if($this->type == 'create_lead_call')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Create new Lead Call') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-phone";
                $icon_color = 'bg-info';
            }

            if($this->type == 'update_lead_source')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Update Sources') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-file-alt";
                $icon_color = 'bg-warning';
            }

            if($this->type == 'add_lead_product')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Add new Products') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-dolly";
                $icon_color = 'bg-danger';
            }

            if($this->type == 'add_lead_discussion')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Add new Discussion') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "fa fa-comments";
                $icon_color = 'bg-info';
            }

            if($this->type == 'move_lead')
            {
                $link       = isset($data->lead_id) ? route('leads.show', [$data->lead_id]) : '#';
                $text       = $usr->name . " " . __('Moved the lead') . " <b class='font-weight-bold'>" . $data->name . "</b> " . __('from') . " " . __(ucwords($data->old_status)) . " " . __('to') . " " . __(ucwords($data->new_status));
                $icon       = "fa fa-arrows-alt";
                $icon_color = 'bg-primary';
            }
            // end Leads

            // Late Attendance Update
            if ($this->type == 'late_attendance_update') {
                $link       = route('attendance.late.log');
                $icon       = 'fa fa-clock';
                $icon_color = 'bg-warning';
                $employee_name = isset($data->employee_name) ? $data->employee_name : '';
                $attendance_date = isset($data->attendance_date) ? $data->attendance_date : '';
                $action = isset($data->action) ? $data->action : 'updated';
                $text = $usr->name . ' ' . __('late') . ' ' . __($action) . ' ' . __('attendance for') . ' <b>' . $employee_name . '</b> (' . $attendance_date . ')';
            }
            // end Late Attendance

            $date = $this->created_at->diffForHumans();
            $html = '<a href="' . $link . '" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="avatar ' . $icon_color . ' text-white rounded-circle"><i class="' . $icon . '"></i></span>
                                    </div>
                                    <div class="flex-fill ml-3">
                                        <div class="h6 text-sm mb-0">' . $text . '</div>
                                        <small class="text-muted text-xs">' . $date . '</small>
                                    </div>
                                </div>
                            </a>';
        }
        else
        {
            $html = '';
        }

        return $html;
    }
}
