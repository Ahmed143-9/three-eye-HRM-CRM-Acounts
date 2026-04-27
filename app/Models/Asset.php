<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'condition',
        'status',
        'purchase_date',
        'supported_date',
        'amount',
        'description',
        'manufacturer',
        'model_number',
        'serial_number',
        'location',
        'warranty_until',
        'image',
        'employee_id',
        'quantity',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'supported_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($asset) {
            if (empty($asset->asset_code)) {
                $asset->asset_code = self::generateAssetCode();
            }
        });
    }

    /**
     * Generate unique asset code
     */
    public static function generateAssetCode()
    {
        $prefix = 'AST';
        $lastAsset = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastAsset ? intval(substr($lastAsset->asset_code, 3)) + 1 : 1;
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get all employee asset assignments
     */
    public function employeeAssets()
    {
        return $this->hasMany(EmployeeAsset::class, 'asset_id');
    }

    /**
     * Get currently assigned employee
     */
    public function currentAssignment()
    {
        return $this->hasOne(EmployeeAsset::class, 'asset_id')
                    ->whereNull('return_date')
                    ->where('status', 'Assigned')
                    ->latest();
    }

    /**
     * Get asset requests
     */
    public function requests()
    {
        return $this->hasMany(AssetRequest::class, 'asset_id');
    }

    /**
     * Get assigned employees (legacy support)
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_assets', 'asset_id', 'employee_id')
                    ->withPivot('assign_date', 'return_date', 'status', 'remarks')
                    ->withTimestamps();
    }

    /**
     * Get users from employee_ids (legacy support)
     */
    public function users($users)
    {
        if (empty($users)) {
            return collect();
        }
        
        $userIds = explode(',', $users);
    
        return User::whereIn('id', $userIds)
            ->with('employee')
            ->get();
    }

    /**
     * Scope: Available assets
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     * Scope: Assigned assets
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'Assigned');
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if asset is available
     */
    public function isAvailable()
    {
        return $this->status === 'Available';
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        $categories = [
            'IT' => 'IT Equipment',
            'Furniture' => 'Furniture',
            'Electronics' => 'Electronics',
            'Vehicles' => 'Vehicles',
            'Machinery' => 'Machinery',
            'Other' => 'Other'
        ];
        
        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'Available' => 'success',
            'Assigned' => 'primary',
            'Lost' => 'danger',
            'Maintenance' => 'warning'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }
}
