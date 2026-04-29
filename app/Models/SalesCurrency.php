<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCurrency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'created_by',
    ];
}
