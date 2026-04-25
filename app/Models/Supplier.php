<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'name',
        'email',
        'phone',
        'tin_no',
        'bin_number',
        'irc_no',
        'contact_person_name',
        'contact_person_number',
        'contact_person_email',
        'head_office_address',
        'factory_address',
        'delivery_address',
        'billing_address',
        'bank_details',
        'file_attachment',
        'is_active',
        'created_by',
    ];
}
