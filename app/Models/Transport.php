<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'unique_id',
        'client_id',
        'manual_client_name',
        'location_address',
        'location_lat',
        'location_lng',
        'driver_name',
        'contact_number',
        'truck_number',
        'starting_date',
        'item_description',
        'delivery_date',
        'lc',
        'ci',
        'payable_id',
        'status',
        'is_seen',
        'created_by',
        'workspace',
    ];

    /**
     * Bill status for display:
     * pending  → no billing amounts entered yet
     * paid     → accountant has saved billing line items
     */
    public function getBillStatus(): string
    {
        return $this->status ?? 'pending';
    }

    public function items()
    {
        return $this->hasMany(TransportItem::class, 'transport_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
