<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetCategory extends Model
{
    use HasFactory;

    protected $table = 'asset_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get assets in this category
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'category', 'code');
    }

    /**
     * Scope: Active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get asset count
     */
    public function getAssetsCountAttribute()
    {
        return $this->assets()->count();
    }

    /**
     * Get available assets count
     */
    public function getAvailableCountAttribute()
    {
        return $this->assets()->where('status', 'Available')->count();
    }

    /**
     * Get assigned assets count
     */
    public function getAssignedCountAttribute()
    {
        return $this->assets()->where('status', 'Assigned')->count();
    }
}
