<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bank Master
        if (!Schema::hasTable('bank_master')) {
            Schema::create('bank_master', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('branch_id')->nullable();
                $table->string('bank_name');
                $table->string('account_number')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('branch_name')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->tinyInteger('status')->default(1);
                $table->integer('isDeleted')->default(0);
                $table->timestamps();
            });

            // Seed default Cash Account
            DB::table('bank_master')->insert([
                'bank_name' => 'Cash Account',
                'account_number' => 'CASH-001',
                'ifsc_code' => 'CASH0001',
                'branch_name' => 'Main Branch',
                'opening_balance' => 0.00,
                'status' => 1,
                'isDeleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Orders (Sales)
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->unsignedBigInteger('user_id')->nullable(); // Customer / Patient user id
                $table->unsignedBigInteger('company_id')->nullable();
                $table->date('order_date')->nullable();
                $table->string('order_type')->nullable(); // sale, challan, medical_report
                $table->integer('is_custom')->default(0);
                $table->decimal('discount', 10, 2)->default(0.00)->nullable();
                $table->decimal('discount_percent', 10, 2)->nullable();
                $table->decimal('discount_amount', 10, 2)->nullable();
                $table->decimal('round_off', 8, 2)->default(0.00)->nullable();
                $table->string('shipping')->nullable();
                $table->string('misc_charges')->nullable();
                $table->text('tax_id')->nullable();
                $table->string('gst_option')->default('without_gst');
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('remaining_amount', 12, 2)->default(0.00);
                $table->string('payment_status')->default('pending'); // paid, pending, partial
                $table->string('payment_method')->default('cash'); // cash, debit, upi, pay_later, cash_online
                $table->string('order_invoice')->nullable();
                $table->string('hospital_status')->nullable();
                $table->string('admin_status')->nullable();
                $table->tinyInteger('admin_approve')->default(0);
                $table->string('delivered_status')->default('delivered');
                $table->text('remarks')->nullable();
                $table->tinyInteger('is_emergency')->default(0);
                $table->tinyInteger('online_portal_sales')->default(0);
                $table->tinyInteger('export_sales')->default(0);
                $table->text('rate_description')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamps();
            });
        }

        // 3. Order Items
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable(); // References medicines.id
                $table->string('custom_product_name')->nullable();
                $table->boolean('is_custom_product')->default(0);
                $table->string('batch_number')->nullable();
                $table->date('expiry_date')->nullable();
                $table->json('product_gst_details')->nullable();
                $table->decimal('product_gst_total', 10, 2)->default(0.00)->nullable();
                $table->decimal('product_discount', 10, 2)->default(0.00)->nullable();
                $table->decimal('product_discount_price', 10, 2)->default(0.00)->nullable();
                $table->json('patient_ids')->nullable();
                $table->integer('scheme_free_qty')->default(0)->nullable();
                $table->decimal('scheme_rate', 10, 2)->default(0.00)->nullable();
                $table->integer('free_qty')->default(0)->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->decimal('price', 12, 2)->default(0.00);
                $table->decimal('mrp', 12, 2)->default(0.00)->nullable();
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('sale_type')->default('local');
                $table->boolean('isDeleted')->default(0);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }

        // 4. Order Patient Pivot
        if (!Schema::hasTable('order_patient')) {
            Schema::create('order_patient', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('patient_id');
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
                $table->unique(['order_id', 'patient_id']);
            });
        }

        // 5. Purchase Invoice
        if (!Schema::hasTable('purchase_invoice')) {
            Schema::create('purchase_invoice', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->unsignedBigInteger('vendor_id'); // References suppliers.id or user.id
                $table->string('bill_no')->nullable();
                $table->json('products')->nullable(); // Full products list JSON
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('paid', 12, 2)->default(0.00)->nullable();
                $table->decimal('discount', 10, 2)->default(0.00)->nullable();
                $table->decimal('shipping', 10, 2)->default(0.00)->nullable();
                $table->text('taxes')->nullable();
                $table->decimal('grand_total', 12, 2)->default(0.00);
                $table->decimal('remaining_amount', 12, 2)->default(0.00)->nullable();
                $table->string('gst_option')->default('without_gst');
                $table->string('status')->default('completed'); // pending, completed, cancelled
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->integer('payment_due_days')->nullable();
                $table->date('due_date')->nullable();
                $table->date('payment_completed_date')->nullable();
                $table->string('interest_type')->nullable();
                $table->decimal('interest_amount', 10, 2)->default(0.00)->nullable();
                $table->decimal('final_amount_after_interest', 12, 2)->nullable();
                $table->json('extraction_metadata')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamps();
            });
        }

        // 6. Purchases (Items)
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->string('item'); // Medicine/Product name or ID
                $table->integer('quantity')->default(1);
                $table->string('batch_no')->nullable();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->decimal('mrp', 10, 2)->default(0.00)->nullable();
                $table->string('mfg_date')->nullable();
                $table->string('exp_date')->nullable();
                $table->integer('free_quantity')->default(0)->nullable();
                $table->json('product_gst_details')->nullable();
                $table->decimal('product_gst_total', 10, 2)->default(0.00)->nullable();
                $table->decimal('price', 12, 2)->default(0.00);
                $table->integer('scheme_free_qty')->default(0)->nullable();
                $table->decimal('scheme_rate', 10, 2)->default(0.00)->nullable();
                $table->decimal('discount_percent', 10, 2)->default(0.00)->nullable();
                $table->decimal('discount_amount', 10, 2)->default(0.00)->nullable();
                $table->date('purchase_date')->nullable();
                $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('completed');
                $table->string('payment_status')->default('paid');
                $table->decimal('amount_total', 12, 2)->default(0.00);
                $table->string('purchase_type')->default('local');
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->string('invoice_id')->nullable();
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->json('extraction_metadata')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamps();
            });
        }

        // 7. Payment Store
        if (!Schema::hasTable('payment_store')) {
            Schema::create('payment_store', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->decimal('payment_amount', 12, 2)->default(0.00);
                $table->date('payment_date');
                $table->string('payment_type')->default('order'); // order, purchase, receipt
                $table->string('payment_method')->default('cash');
                $table->string('cash_amount')->default('0');
                $table->string('upi_amount')->default('0');
                $table->string('emi_month')->nullable();
                $table->string('remaining_amount')->default('0');
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('isDeleted')->default(0);
                $table->timestamps();
            });
        }

        // 8. Add Vitals and clinical history fields to patients table if not already present
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'bp')) {
                $table->string('bp', 50)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('patients', 'temperature')) {
                $table->string('temperature', 50)->nullable()->after('bp');
            }
            if (!Schema::hasColumn('patients', 'pulse')) {
                $table->string('pulse', 50)->nullable()->after('temperature');
            }
            if (!Schema::hasColumn('patients', 'patient_history')) {
                $table->text('patient_history')->nullable()->after('pulse');
            }
            if (!Schema::hasColumn('patients', 'patient_complaint')) {
                $table->text('patient_complaint')->nullable()->after('patient_history');
            }
            if (!Schema::hasColumn('patients', 'advice_for_lab_tests')) {
                $table->text('advice_for_lab_tests')->nullable()->after('patient_complaint');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_store');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_invoice');
        Schema::dropIfExists('order_patient');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('bank_master');
    }
};

