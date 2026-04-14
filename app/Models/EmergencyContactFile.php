<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContactFile extends Model
{
    protected $fillable = [
        'emergency_contact_id',
        'file_name',
        'original_name',
        'file_type',
        'file_size',
        'created_by',
    ];

    public function emergencyContact()
    {
        return $this->belongsTo(EmployeeEmergencyContact::class, 'emergency_contact_id');
    }
}
