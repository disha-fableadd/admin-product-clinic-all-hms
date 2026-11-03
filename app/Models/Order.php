<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'company_id',
        'is_custom',
        'order_date',
        'order_type',
        'discount',
        'discount_percent',
        'discount_amount',
        'round_off',
        'shipping',
        'misc_charges',
        'tax_id',
        'gst_option',
        'branch_id',
        'created_by',
        'total_amount',
        'remaining_amount',
        'payment_status',
        'payment_method',
        'order_invoice',
        'hospital_status',
        'admin_status',
        'admin_approve',
        'delivered_status',
        'remarks',
        'is_emergency',
        'online_portal_sales',
        'export_sales',
        'rate_description',
        'isDeleted',
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'order_id', 'id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patients::class, 'order_patient', 'order_id', 'patient_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $latest = static::max('id') ?? 0;
                $model->order_number = 'ORD-' . str_pad($latest + 1, 6, '0', STR_PAD_LEFT);
            }
            if (empty($model->order_date)) {
                $model->order_date = Carbon::now('Asia/Kolkata')->toDateString();
            }
        });
    }
}

