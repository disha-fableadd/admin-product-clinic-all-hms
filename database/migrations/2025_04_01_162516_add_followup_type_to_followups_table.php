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
        if (Schema::hasTable('followups')) {
            Schema::table('followups', function (Blueprint $table) {
                if (!Schema::hasColumn('followups', 'followup_type')) {
                    $table->enum('followup_type', [
                        'Regular followup ',
                        'Appointment related',
                        'Medicine related',
                        'Discharge related ',
                        'Report related',
                        'other'
                    ])->nullable()->after('followup_update');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('followups')) {
            Schema::table('followups', function (Blueprint $table) {
                if (Schema::hasColumn('followups', 'followup_type')) {
                    $table->dropColumn('followup_type');
                }
            });
        }
    }
};