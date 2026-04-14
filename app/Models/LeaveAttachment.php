<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveAttachment extends Model
{
    protected $fillable = [
        'leave_id',
        'employee_id',
        'file_name',
        'original_name',
        'file_path',
        'created_by',
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
}
