<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'description',
        'status',
        'branch_id',
        'user_id',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'brand_id');
    }
}

