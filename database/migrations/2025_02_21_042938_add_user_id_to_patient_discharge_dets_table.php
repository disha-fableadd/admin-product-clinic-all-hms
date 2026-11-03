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
        if (Schema::hasTable('patient_discharge_dets')) {
            Schema::table('patient_discharge_dets', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_discharge_dets', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patient_discharge_dets')) {
            Schema::table('patient_discharge_dets', function (Blueprint $table) {
                if (Schema::hasColumn('patient_discharge_dets', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};