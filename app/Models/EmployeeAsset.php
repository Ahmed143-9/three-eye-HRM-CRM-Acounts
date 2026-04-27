<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAsset extends Model
{
    use HasFactory;

    protected $table = 'employee_assets';

    protected $fillable = [
        'employee_id',
        'asset_id',
        'assign_date',
        'return_date',
        'status',
        'remarks',
        'document',
        'asset_name',
        'description',
        'image',
        'assigned_by',
        'created_by',
    ];

    protected $casts = [
        'assign_date' => 'date',
        'return_date' => 'date',
    ];

    /**
     * Get the employee that owns the asset assignment
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the asset that is assigned
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the user who assigned the asset
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope: Currently assigned assets
     */
    public function scopeCurrentlyAssigned($query)
    {
        return $query->whereNull('return_date')
                     ->where('status', 'Assigned');
    }

    /**
     * Scope: Returned assets
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'Returned');
    }

    /**
     * Check if asset is currently assigned
     */
    public function isCurrentlyAssigned()
    {
        return is_null($this->return_date) && $this->status === 'Assigned';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'Assigned' => 'primary',
            'Returned' => 'success',
            'Lost' => 'danger',
            'Damaged' => 'warning'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }
}
