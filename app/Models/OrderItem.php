<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'custom_product_name',
        'is_custom_product',
        'batch_number',
        'expiry_date',
        'product_gst_details',
        'product_gst_total',
        'product_discount',
        'product_discount_price',
        'patient_ids',
        'scheme_free_qty',
        'scheme_rate',
        'free_qty',
        'category_id',
        'quantity',
        'price',
        'mrp',
        'total_amount',
        'branch_id',
        'created_by',
        'sale_type',
        'isDeleted',
    ];

    protected $casts = [
        'product_gst_details' => 'array',
        'patient_ids' => 'array',
        'scheme_free_qty' => 'integer',
        'scheme_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'is_custom_product' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Medicine::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

