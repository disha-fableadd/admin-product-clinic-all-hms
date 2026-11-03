<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Brands Table
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 2. Stock Units Table
        if (!Schema::hasTable('stock_units')) {
            Schema::create('stock_units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });

            // Seed default units if empty
            $defaultUnits = [
                ['name' => 'Box', 'short_name' => 'BOX'],
                ['name' => 'Strip', 'short_name' => 'STRIP'],
                ['name' => 'Tablet', 'short_name' => 'TAB'],
                ['name' => 'Capsule', 'short_name' => 'CAP'],
                ['name' => 'Bottle', 'short_name' => 'BTL'],
                ['name' => 'Vial', 'short_name' => 'VIAL'],
                ['name' => 'Ampoule', 'short_name' => 'AMP'],
                ['name' => 'Tube', 'short_name' => 'TUBE'],
                ['name' => 'Piece', 'short_name' => 'PCS'],
            ];
            foreach ($defaultUnits as $unit) {
                DB::table('stock_units')->insert(array_merge($unit, [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }

        // 3. Product Barcodes Table
        if (!Schema::hasTable('product_barcodes')) {
            Schema::create('product_barcodes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('barcode');
                $table->timestamps();
            });
        }

        // 4. Update Medicines Table
        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                if (!Schema::hasColumn('medicines', 'service_type')) {
                    $table->string('service_type')->default('Medicine')->after('id');
                }
                if (!Schema::hasColumn('medicines', 'brand_id')) {
                    $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
                }
                if (!Schema::hasColumn('medicines', 'low_stock_qty')) {
                    $table->integer('low_stock_qty')->nullable()->after('quantity');
                }
                if (!Schema::hasColumn('medicines', 'stock_unit_id')) {
                    $table->unsignedBigInteger('stock_unit_id')->nullable()->after('medicine_unit');
                }
                if (!Schema::hasColumn('medicines', 'mrp')) {
                    $table->decimal('mrp', 12, 2)->nullable()->after('product_gst');
                }
                if (!Schema::hasColumn('medicines', 'purchase_price')) {
                    $table->decimal('purchase_price', 12, 2)->nullable()->after('mrp');
                }
                if (!Schema::hasColumn('medicines', 'stock_value')) {
                    $table->decimal('stock_value', 12, 2)->nullable()->after('purchase_price');
                }
                if (!Schema::hasColumn('medicines', 'availablility')) {
                    $table->string('availablility')->default('in_stock')->after('status');
                }
                if (!Schema::hasColumn('medicines', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('availablility');
                }
                if (!Schema::hasColumn('medicines', 'barcode')) {
                    $table->string('barcode')->nullable()->after('batch_no');
                }
                if (!Schema::hasColumn('medicines', 'pack')) {
                    $table->string('pack')->nullable()->after('barcode');
                }
                if (!Schema::hasColumn('medicines', 'hsn_code')) {
                    $table->string('hsn_code')->nullable()->after('pack');
                }
            });

            // Set existing MRP from unit price where mrp is null
            DB::statement("UPDATE medicines SET mrp = CAST(unit AS DECIMAL(12,2)) WHERE mrp IS NULL AND unit REGEXP '^[0-9]+(\\\\.[0-9]+)?$'");
        }

        // 5. Update Suppliers (Vendors) Table
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suppliers', 'gst_number')) {
                    $table->string('gst_number')->nullable()->after('email');
                }
                if (!Schema::hasColumn('suppliers', 'drug_license_no')) {
                    $table->string('drug_license_no')->nullable()->after('gst_number');
                }
                if (!Schema::hasColumn('suppliers', 'pan_no')) {
                    $table->string('pan_no')->nullable()->after('drug_license_no');
                }
                if (!Schema::hasColumn('suppliers', 'bank_name')) {
                    $table->string('bank_name')->nullable()->after('pan_no');
                }
                if (!Schema::hasColumn('suppliers', 'account_no')) {
                    $table->string('account_no')->nullable()->after('bank_name');
                }
                if (!Schema::hasColumn('suppliers', 'ifsc_code')) {
                    $table->string('ifsc_code')->nullable()->after('account_no');
                }
                if (!Schema::hasColumn('suppliers', 'bank_branch')) {
                    $table->string('bank_branch')->nullable()->after('ifsc_code');
                }
                if (!Schema::hasColumn('suppliers', 'opening_balance')) {
                    $table->decimal('opening_balance', 12, 2)->default(0)->after('bank_branch');
                }
                if (!Schema::hasColumn('suppliers', 'closing_balance')) {
                    $table->decimal('closing_balance', 12, 2)->default(0)->after('opening_balance');
                }
                if (!Schema::hasColumn('suppliers', 'balance_year')) {
                    $table->integer('balance_year')->nullable()->after('closing_balance');
                }
            });
        }
    }

    public function down(): void
    {
        // Safe down
        Schema::dropIfExists('brands');
        Schema::dropIfExists('stock_units');
        Schema::dropIfExists('product_barcodes');
    }
};

