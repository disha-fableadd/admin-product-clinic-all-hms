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
        if (Schema::hasTable('patient_medicine')) {
            Schema::table('patient_medicine', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_medicine', 'appointment_id')) {
                    $table->unsignedBigInteger('appointment_id')->nullable()->after('medicine_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patient_medicine')) {
            Schema::table('patient_medicine', function (Blueprint $table) {
                if (Schema::hasColumn('patient_medicine', 'appointment_id')) {
                    $table->dropColumn('appointment_id');
                }
            });
        }
    }
};