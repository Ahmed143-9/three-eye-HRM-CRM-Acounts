<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesConsignmentNote extends Model
{
    protected $table = 'sales_consignment_notes';

    protected $fillable = [
        'order_id',
        'ci_id',
        'file_path',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function ci()
    {
        return $this->belongsTo(SalesCI::class, 'ci_id');
    }

    public function weightSlips()
    {
        return $this->hasMany(SalesWeightSlip::class, 'consignment_note_id');
    }
}
