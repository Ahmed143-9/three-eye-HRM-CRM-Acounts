<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'title',
        'days',
        'is_attachment_required',
        'min_advance_days',
        'is_default',
        'created_by',
    ];
}
