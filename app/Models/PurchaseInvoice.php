<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $table = 'purchase_invoice';

    protected $fillable = [
        'extraction_metadata',
        'branch_id',
        'invoice_number',
        'vendor_id',
        'bill_no',
        'products',
        'total_amount',
        'paid',
        'discount',
        'shipping',
        'taxes',
        'grand_total',
        'remaining_amount',
        'gst_option',
        'status',
        'isDeleted',
        'created_by',
        'updated_by',
        'payment_due_days',
        'due_date',
        'payment_completed_date',
        'interest_type',
        'interest_amount',
        'final_amount_after_interest',
    ];

    protected $casts = [
        'extraction_metadata' => 'array',
        'products' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchases::class, 'invoice_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'invoice_id', 'id');
    }
}

