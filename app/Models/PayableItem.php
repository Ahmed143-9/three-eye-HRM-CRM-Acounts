<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayableItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_id',
        'serial',
        'order_details',
        'qty',
        'rate',
        'amount',
    ];

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }
}
