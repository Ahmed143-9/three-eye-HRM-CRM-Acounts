<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesWeightSlip extends Model
{
    protected $table = 'sales_weight_slips';

    protected $fillable = [
        'consignment_note_id',
        'tanker_id',
        'in_out_number',
        'gross_weight',
        'tare_weight',
        'net_weight',
    ];

    public function consignmentNote()
    {
        return $this->belongsTo(SalesConsignmentNote::class, 'consignment_note_id');
    }

    public function tanker()
    {
        return $this->belongsTo(SalesCITanker::class, 'tanker_id');
    }
}
