<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportItem extends Model
{
    protected $fillable = [
        'transport_id',
        'description',
        'amount',
    ];

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }
}
