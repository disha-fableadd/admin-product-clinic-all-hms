<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $table = 'taxes';

    protected $fillable = [
        'tax_name',
        'tax_rate',
        'status',
        'branch_id',
        'isDeleted',
    ];
}
