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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'subtotal')) {
                    $table->bigInteger('subtotal')->default(0)->after('grand_total');
                }
                if (!Schema::hasColumn('invoices', 'tax')) {
                    $table->bigInteger('tax')->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('invoices', 'discount')) {
                    $table->bigInteger('discount')->default(0)->after('tax');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $cols = array_filter(['tax', 'discount', 'subtotal'], function($col) {
                    return Schema::hasColumn('invoices', $col);
                });
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};