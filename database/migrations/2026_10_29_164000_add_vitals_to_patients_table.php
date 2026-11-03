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
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'bp')) {
                $table->string('bp')->nullable();
            }
            if (!Schema::hasColumn('patients', 'pulse')) {
                $table->string('pulse')->nullable();
            }
            if (!Schema::hasColumn('patients', 'temperature')) {
                $table->string('temperature')->nullable();
            }
            if (!Schema::hasColumn('patients', 'weight')) {
                $table->string('weight')->nullable();
            }
            if (!Schema::hasColumn('patients', 'height')) {
                $table->string('height')->nullable();
            }
            if (!Schema::hasColumn('patients', 'patient_history')) {
                $table->text('patient_history')->nullable();
            }
            if (!Schema::hasColumn('patients', 'patient_complaint')) {
                $table->text('patient_complaint')->nullable();
            }
            if (!Schema::hasColumn('patients', 'advice_for_lab_tests')) {
                $table->text('advice_for_lab_tests')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'weight',
                'height',
            ]);
        });
    }
};

