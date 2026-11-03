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
                if (!Schema::hasColumn('medical_reports', 'user_id')) {
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
        if (Schema::hasTable('medical_reports')) {
            Schema::table('medical_reports', function (Blueprint $table) {
                if (Schema::hasColumn('medical_reports', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};