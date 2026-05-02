<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCITanker extends Model
{
    protected $table = 'sales_ci_tankers';

    protected $fillable = [
        'ci_id',
        'tanker_number',
        'quantity_mt',
        'quantity_unit',
        'cpt_usd',
        'currency',
        'total_amount_usd',
        'file_path',
    ];

    public function ci()
    {
        return $this->belongsTo(SalesCI::class, 'ci_id');
    }

    public function weightSlip()
    {
        return $this->hasOne(SalesWeightSlip::class, 'tanker_id');
    }
}
