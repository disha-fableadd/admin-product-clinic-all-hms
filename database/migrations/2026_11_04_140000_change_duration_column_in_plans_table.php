<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'duration')) {
            try {
                DB::statement("ALTER TABLE `plans` MODIFY COLUMN `duration` VARCHAR(255) NULL");
            } catch (\Throwable $e) {
                // Ignore if alter is not permitted
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
