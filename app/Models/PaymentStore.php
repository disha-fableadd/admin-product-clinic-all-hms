<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStore extends Model
{
    use HasFactory;

    protected $table = 'payment_store';

    protected $fillable = [
        'user_id',
        'order_id',
        'invoice_id',
        'payment_amount',
        'payment_date',
        'payment_type',
        'payment_method',
        'cash_amount',
        'upi_amount',
        'emi_month',
        'remaining_amount',
        'remarks',
        'isDeleted',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

