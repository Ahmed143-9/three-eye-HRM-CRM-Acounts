<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmergencyContact extends Model
{
    protected $fillable = [
        'employee_id',
        'full_name',
        'relationship',
        'contact_number',
        'email',
        'address',
        'nid_file',
        'is_primary',
        'created_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function files()
    {
        return $this->hasMany(EmergencyContactFile::class, 'emergency_contact_id');
    }
}
