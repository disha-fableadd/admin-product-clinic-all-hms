<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUnit extends Model
{
    use HasFactory;

    protected $table = 'stock_units';

    protected $fillable = [
        'name',
        'short_name',
        'status',
        'branch_id',
        'user_id',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'stock_unit_id');
    }
}

