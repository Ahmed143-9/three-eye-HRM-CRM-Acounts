<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesWeightSlip extends Model
{
    protected $table = 'sales_weight_slips';

    protected $fillable = [
        'consignment_note_id',
        'tanker_id', // This now stores tanker_number as a string
        'in_out_number',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'file_path',
    ];

    public function consignmentNote()
    {
        return $this->belongsTo(SalesConsignmentNote::class, 'consignment_note_id');
    }

    /**
     * Get the associated CI Tanker by tanker_number (stored in tanker_id column)
     */
    public function ciTanker()
    {
        // Get the order_id from consignment note to scope the tanker search
        $cn = $this->consignmentNote;
        if (!$cn) return null;
        
        $ci = SalesCI::where('order_id', $cn->order_id)->first();
        if (!$ci) return null;

        return SalesCITanker::where('ci_id', $ci->id)
            ->where('tanker_number', $this->tanker_id)
            ->first();
    }
}
