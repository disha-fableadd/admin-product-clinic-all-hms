<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Services\PlanPermissionSyncService;

class ModulesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_permissions')->truncate();
        DB::table('modules')->truncate();
        DB::statement("SET sql_mode='NO_AUTO_VALUE_ON_ZERO';");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $plan = Plan::where('is_active', 1)->latest('id')->first()
            ?: Plan::latest('id')->first();

        PlanPermissionSyncService::syncPlanModulesAndPermissions($plan);
    }
}

