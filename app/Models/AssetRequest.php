<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetRequest extends Model
{
    use HasFactory;

    protected $table = 'asset_requests';

    protected $fillable = [
        'employee_id',
        'asset_id',
        'status',
        'reason',
        'rejection_reason',
        'approved_by',
        'requested_date',
        'approved_date',
        'created_by',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'approved_date' => 'date',
    ];

    /**
     * Get the employee who requested the asset
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the requested asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the user who approved/rejected
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope: Approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope: Rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }
}
