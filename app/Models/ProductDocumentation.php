<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDocumentation extends Model
{
    use HasFactory;

    protected $table = 'product_documentations';

    protected $fillable = [
        'extraction_metadata',
        'product_id',
        'order_id',
        'customer_id',
        'batch_number',
        'mfg_date',
        'expiry_date',
        'coa_pdfs',
        'created_by',
    ];

    protected $casts = [
        'extraction_metadata' => 'array',
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'coa_pdfs' => 'array',
    ];

    /**
     * Relationship with Medicine / Product
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'product_id');
    }

    public function product()
    {
        return $this->belongsTo(Medicine::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

