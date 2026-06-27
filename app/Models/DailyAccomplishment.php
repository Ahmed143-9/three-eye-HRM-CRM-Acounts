<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAccomplishment extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'summary',
        'challenges',
        'hours_spent',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
