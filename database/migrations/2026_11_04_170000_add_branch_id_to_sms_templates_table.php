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
        if (Schema::hasTable('sms_templates')) {
            Schema::table('sms_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('sms_templates', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('user_id')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sms_templates')) {
            Schema::table('sms_templates', function (Blueprint $table) {
                if (Schema::hasColumn('sms_templates', 'branch_id')) {
                    $table->dropColumn('branch_id');
                }
            });
        }
    }
};

