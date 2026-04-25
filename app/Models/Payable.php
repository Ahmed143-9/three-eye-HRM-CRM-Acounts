<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Consultant;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'invoice_number',
        'date',
        'billing_direction',
        'entity_id',
        'billing_address',
        'total_amount',
        'status',
        'next_due_date',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(PayableItem::class, 'payable_id', 'id');
    }

    public function payments()
    {
        return $this->morphMany(BillingPayment::class, 'billable');
    }

    public function getTotalPaid()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalAdjustment()
    {
        return $this->payments()->sum('adjustment_amount');
    }

    public function getDueAmount()
    {
        return $this->total_amount - $this->getTotalPaid() - $this->getTotalAdjustment();
    }

    public function getCalculatedStatus()
    {
        $paid        = (float) $this->getTotalPaid();
        $adjustment  = (float) $this->getTotalAdjustment();
        $total       = (float) $this->total_amount;
        $settled     = $paid + $adjustment;

        if ($settled <= 0) {
            return 'due';
        } elseif ($settled >= $total) {
            return 'paid';
        } else {
            return 'partial paid';
        }
    }

    public function getPartyName()
    {
        $direction = strtolower($this->billing_direction);
        if ($direction == 'client') {
            $entity = Client::find($this->entity_id);
        } elseif ($direction == 'supplier') {
            $entity = Supplier::find($this->entity_id);
        } elseif ($direction == 'consultant') {
            $entity = Consultant::find($this->entity_id);
        } else {
            $entity = null;
        }

        return $entity ? $entity->name : '-';
    }
}
