<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function getLatest()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::user()->id)
            ->where('is_read', 0)
            ->count();

        $latestNotification = Notification::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $html = '';
        foreach ($notifications as $notification) {
            $html .= $notification->toHtml();
        }

        if (empty($html)) {
            $html = '<div class="text-center p-3 text-muted">' . __('No notifications found') . '</div>';
        }

        return response()->json([
            'html' => $html,
            'unreadCount' => $unreadCount,
            'latestId' => $latestNotification ? $latestNotification->id : 0,
            'latestNotification' => $latestNotification
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::user()->id)->find($id);
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::user()->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return redirect()->back()->with('success', __('All notifications marked as read.'));
    }

    public function readAndRedirect($id)
    {
        $notification = Notification::where('user_id', Auth::user()->id)->find($id);
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();

            // Determine redirect URL
            $url = route('dashboard');
            $data = json_decode($notification->data);

            if ($notification->related_model == 'ErpExpense') {
                $expense = \App\Models\ErpExpense::find($notification->related_id);
                if ($expense) {
                    $url = route('erp-expenses.index', 'approvals') . '?open_id=' . $expense->id;
                }
            } elseif ($notification->related_model == 'ErpSalarySheet') {
                $url = route('erp-expenses.index', 'approvals') . '?open_salary_id=' . $notification->related_id;
            } elseif (isset($data->deal_id)) {
                $url = route('deals.show', [$data->deal_id]);
            } elseif (isset($data->lead_id)) {
                $url = route('leads.show', [$data->lead_id]);
            } elseif (isset($data->estimation_id)) {
                $url = route('estimations.show', [$data->estimation_id]);
            } elseif ($notification->type == 'late_attendance_update') {
                $url = route('attendance.late.log');
            }

            return redirect($url);
        }
        return redirect()->back()->with('error', __('Notification not found.'));
    }
}
