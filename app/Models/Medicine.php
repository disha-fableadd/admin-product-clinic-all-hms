<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Medicine extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'medicines';

    protected $fillable = [
        'service_type',
        'category_id',
        'brand_id',
        'name',
        'description',
        'unit',
        'status',
        'availablility',
        'is_featured',
        'quantity',
        'low_stock_qty',
        'medicine_unit',
        'stock_unit_id',
        'manufacture_date',
        'expiry_date',
        'batch_no',
        'barcode',
        'pack',
        'hsn_code',
        'gst_option',
        'product_gst',
        'mrp',
        'purchase_price',
        'stock_value',
        'image',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'product_gst' => 'array',
        'is_featured' => 'boolean',
        'mrp' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'stock_value' => 'decimal:2',
    ];

    protected array $encryptedAttributes = ['name', 'description', 'unit', 'batch_no'];

    protected static function booted()
    {
        static::saving(function ($medicine) {
            // Auto sync mrp and unit
            if (empty($medicine->mrp) && !empty($medicine->unit) && is_numeric($medicine->unit)) {
                $medicine->mrp = (float) $medicine->unit;
            } elseif (!empty($medicine->mrp) && empty($medicine->unit)) {
                $medicine->unit = (string) $medicine->mrp;
            }

            // Auto calculate stock value
            $qty = (float) ($medicine->quantity ?? 0);
            $cost = (float) ($medicine->purchase_price ?: ($medicine->mrp ?: 0));
            $medicine->stock_value = round($qty * $cost, 2);

            // Auto set availability
            $medicine->availablility = $qty > 0 ? 'in_stock' : 'out_stock';

            // Auto barcode if empty
            if (empty($medicine->barcode)) {
                $medicine->barcode = 'MED' . mt_rand(1000000000, 9999999999);
            }
        });
    }

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    // Relationship with Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Relationship with Stock Unit
    public function stockUnit()
    {
        return $this->belongsTo(StockUnit::class, 'stock_unit_id');
    }

    // Barcodes
    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class, 'medicine_id');
    }

    public function getImageAttribute($value)
    {
        if (!$value) {
            return asset(env('IMAGE_PATH') . '/admin/assets/img/default.webp');
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (filter_var(env('IMAGE_PATH'), FILTER_VALIDATE_URL)) {
            return rtrim(env('IMAGE_PATH'), '/') . '/' . $value;
        }

        return asset(env('IMAGE_PATH') . $value);
    }

    public function patients()
    {
        return $this->belongsToMany(Patients::class, 'patient_medicine')->withPivot('note')->withTimestamps();
    }

    public function patientMedicines()
    {
        return $this->hasMany(PatientMedicine::class, 'medicine_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function documentations()
    {
        return $this->hasMany(ProductDocumentation::class, 'product_id');
    }
}
