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
        if (Schema::hasTable('radiology_reports')) {
            Schema::table('radiology_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('radiology_reports', 'converted_image')) {
                    $table->string('converted_image')->nullable()->after('report_file');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('radiology_reports')) {
            Schema::table('radiology_reports', function (Blueprint $table) {
                if (Schema::hasColumn('radiology_reports', 'converted_image')) {
                    $table->dropColumn('converted_image');
                }
            });
        }
    }
};