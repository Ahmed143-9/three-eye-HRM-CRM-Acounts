<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivableItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'receivable_id',
        'serial',
        'order_details',
        'qty',
        'rate',
        'amount',
    ];

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }
}
