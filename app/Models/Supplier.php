<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'gst_number',
        'drug_license_no',
        'pan_no',
        'bank_name',
        'account_no',
        'ifsc_code',
        'bank_branch',
        'opening_balance',
        'closing_balance',
        'balance_year',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    // Relationship with Inventory
    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
