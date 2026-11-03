<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'extraction_metadata',
        'branch_id',
        'item',
        'quantity',
        'batch_no',
        'brand_id',
        'mrp',
        'mfg_date',
        'exp_date',
        'free_quantity',
        'product_gst_details',
        'product_gst_total',
        'price',
        'scheme_free_qty',
        'scheme_rate',
        'discount_percent',
        'discount_amount',
        'purchase_date',
        'purchase_status',
        'payment_status',
        'amount_total',
        'purchase_type',
        'vendor_id',
        'invoice_id',
        'isDeleted',
        'created_by',
    ];

    protected $casts = [
        'extraction_metadata' => 'array',
        'product_gst_details' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Medicine::class, 'item', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'item', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'invoice_id', 'id');
    }
}

