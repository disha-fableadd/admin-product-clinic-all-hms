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
        if (Schema::hasTable('medical_reports') && !Schema::hasColumn('medical_reports', 'branch_id')) {
            Schema::table('medical_reports', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('medical_reports') && Schema::hasColumn('medical_reports', 'branch_id')) {
            Schema::table('medical_reports', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
