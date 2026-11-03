<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMaster extends Model
{
    use HasFactory;

    protected $table = 'bank_master';

    protected $fillable = [
        'branch_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'opening_balance',
        'status',
        'isDeleted',
    ];
}

