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
        if (Schema::hasTable('medical_reports')) {
            Schema::table('medical_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('medical_reports', 'date')) {
                    $table->date('date')->nullable()->after('patient_id');
                }
                if (!Schema::hasColumn('medical_reports', 'report_type')) {
                    $table->string('report_type')->nullable()->after('date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('medical_reports')) {
            Schema::table('medical_reports', function (Blueprint $table) {
                if (Schema::hasColumn('medical_reports', 'date')) {
                    $table->dropColumn('date');
                }
                if (Schema::hasColumn('medical_reports', 'report_type')) {
                    $table->dropColumn('report_type');
                }
            });
        }
    }
};
