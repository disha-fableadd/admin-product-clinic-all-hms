<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlanPermissionSyncService
{
    /**
     * Standard 36 modules for Clinic / HMS.
     */
    public static function getStandardModules(): array
    {
        return [
            ['id' => 1,  'name' => 'Dashboard'],
            ['id' => 2,  'name' => 'Followup'],
            ['id' => 3,  'name' => 'Staffs'],
            ['id' => 4,  'name' => 'Medicines'],
            ['id' => 5,  'name' => 'Patients'],
            ['id' => 6,  'name' => 'Appointments'],
            ['id' => 7,  'name' => 'Treatments'],
            ['id' => 8,  'name' => 'Diagnostic Services'],
            ['id' => 9,  'name' => 'Calender'],
            ['id' => 10, 'name' => 'Discharge'],
            ['id' => 11, 'name' => 'Inventory'],
            ['id' => 12, 'name' => 'Supplier'],
            ['id' => 13, 'name' => 'Medical Report'],
            ['id' => 14, 'name' => 'Report'],
            ['id' => 15, 'name' => 'Sms'],
            ['id' => 16, 'name' => 'Email'],
            ['id' => 17, 'name' => 'PatientMedicine'],
            ['id' => 18, 'name' => 'Invoice'],
            ['id' => 19, 'name' => 'Pathology'],
            ['id' => 20, 'name' => 'Pathology Reports'],
            ['id' => 21, 'name' => 'Radiology Tests'],
            ['id' => 22, 'name' => 'Radiology Reports'],
            ['id' => 23, 'name' => 'Opd Visits'],
            ['id' => 24, 'name' => 'OT'],
            ['id' => 25, 'name' => 'Ipd Admit'],
            ['id' => 26, 'name' => 'Therapy'],
            ['id' => 27, 'name' => 'Assigned Therapy'],
            ['id' => 28, 'name' => 'Daily Data'],
            ['id' => 29, 'name' => 'Payment History'],
            ['id' => 30, 'name' => 'Branch'],
            ['id' => 31, 'name' => 'Assessment'],
            ['id' => 32, 'name' => 'Home Advice'],
            ['id' => 33, 'name' => 'Soap'],
            ['id' => 34, 'name' => 'Machine'],
            ['id' => 35, 'name' => 'Tax Rate'],
            ['id' => 36, 'name' => 'Referal Doctor'],
            ['id' => 37, 'name' => 'Sales Report'],
            ['id' => 38, 'name' => 'Purchase Report'],
            ['id' => 39, 'name' => 'Expense Report'],
            ['id' => 40, 'name' => 'Analytics & Summary'],
        ];
    }

    /**
     * Mapping of Clinic / HMS module IDs to their name aliases / keywords.
     */
    public static function getModuleAliasMap(): array
    {
        return [
            1  => ['dashboard'],
            2  => ['followup', 'follow up', 'followups', 'all followup'],
            3  => ['staffs', 'staff', 'user', 'users', 'all staffs', 'all staff', 'employees', 'employee'],
            4  => ['medicines', 'medicine', 'all medicines', 'pharmacy'],
            5  => ['patients', 'patient', 'all patients', 'add patient', 'diagnosis'],
            6  => ['appointments', 'appointment', 'all appointments', 'add appointment'],
            7  => ['treatments', 'treatment', 'all treatments', 'add treatment', 'book treatment'],
            8  => ['diagnostic services', 'diagnostic service', 'service master'],
            9  => ['calendar', 'calender'],
            10 => ['discharge', 'ot & discharge'],
            11 => ['inventory', 'stock / inventory', 'stock', 'manage inventory', 'product inventory'],
            12 => ['supplier', 'vendors', 'vendor', 'all vendors', 'suppliers'],
            13 => ['medical report', 'medical reports', 'all medical reports'],
            14 => ['reports', 'report', 'sales report', 'purchase report', 'expense report', 'analytics & summary'],
            15 => ['sms', 'sms credentials', 'settings', 'general settings', 'shop settings'],
            16 => ['email', 'smtp credentials', 'settings', 'general settings'],
            17 => ['patientmedicine', 'patient medicine', 'medicines', 'medicine'],
            18 => ['invoice', 'invoices', 'sale bill', 'sale bills', 'sales', 'sales & bills'],
            19 => ['pathology', 'pathology tests', 'diagnostic services', 'diagnostic service'],
            20 => ['pathology reports', 'sale bill', 'sale bills', 'sales'],
            21 => ['radiology tests', 'purchase bill', 'purchase bills', 'purchases', 'diagnostic services'],
            22 => ['radiology reports', 'diagnostic services'],
            23 => ['opd visits', 'opd', 'opd visit', 'clinical services'],
            24 => ['ot', 'ot procedure', 'clinical services', 'ot & discharge'],
            25 => ['ipd admit', 'ipd', 'clinical services'],
            26 => ['therapy', 'therapies', 'clinical services'],
            27 => ['assigned therapy', 'therapy', 'clinical services'],
            28 => ['daily data', 'daily register', 'clinical services'],
            29 => ['payment history', 'my plan details', 'plans', 'membership'],
            30 => ['branch', 'branches', 'my branch'],
            31 => ['assessment', 'account ledger', 'clinical notes', 'accounting'],
            32 => ['home advice', 'clinical notes'],
            33 => ['soap', 'clinical notes'],
            34 => ['machine', 'diet chart', 'diet charts', 'treatments'],
            35 => ['tax rate', 'tax rates', 'tax'],
            36 => ['referal doctor', 'referral doctor', 'patients'],
            37 => ['sales report', 'sales-report', 'sales reports'],
            38 => ['purchase report', 'purchase-report', 'purchase reports'],
            39 => ['expense report', 'expense-report', 'expense reports', 'expenses report'],
            40 => ['analytics & summary', 'analytics', 'charts', 'chart'],
        ];
    }

    /**
     * Resolve allowed module IDs based on plan features, tenant assigned modules, and sub-modules.
     */
    public static function resolveAllowedModuleIds(mixed $features = null, mixed $subModules = null): array
    {
        $rawItems = [];

        // Parse features
        if (!empty($features)) {
            if (is_string($features)) {
                $decoded = json_decode($features, true);
                if (is_array($decoded)) {
                    $rawItems = array_merge($rawItems, $decoded);
                } else {
                    $rawItems = array_merge($rawItems, preg_split('/[\n,]+/', $features));
                }
            } elseif (is_array($features)) {
                $rawItems = array_merge($rawItems, $features);
            }
        }

        // Include tenant assigned modules if available
        if (function_exists('tenant') && ($tMods = tenant('modules'))) {
            if (is_array($tMods)) {
                $rawItems = array_merge($rawItems, $tMods);
            } elseif (is_string($tMods)) {
                $decoded = json_decode($tMods, true);
                if (is_array($decoded)) {
                    $rawItems = array_merge($rawItems, $decoded);
                } else {
                    $rawItems = array_merge($rawItems, preg_split('/[\n,]+/', $tMods));
                }
            }
        }

        // Parse sub_modules
        if (!empty($subModules)) {
            if (is_string($subModules)) {
                $decoded = json_decode($subModules, true);
                if (is_array($decoded)) {
                    $rawItems = array_merge($rawItems, $decoded);
                } else {
                    $rawItems = array_merge($rawItems, preg_split('/[\n,]+/', $subModules));
                }
            } elseif (is_array($subModules)) {
                $rawItems = array_merge($rawItems, $subModules);
            }
        }

        $rawItems = array_values(array_filter(array_map(fn($i) => strtolower(trim((string) $i)), $rawItems)));

        // If no features or submodules are provided, default to all standard modules
        if (empty($rawItems)) {
            return array_column(self::getStandardModules(), 'id');
        }

        $aliasMap = self::getModuleAliasMap();
        $allowedIds = [1]; // Dashboard is always allowed

        foreach ($aliasMap as $modId => $aliases) {
            foreach ($aliases as $alias) {
                if (in_array($alias, $rawItems, true)) {
                    $allowedIds[] = $modId;
                    break;
                }
            }
        }

        return array_values(array_unique($allowedIds));
    }

    /**
     * Synchronize modules and user_permissions according to the active/specified plan.
     */
    public static function syncPlanModulesAndPermissions(?Plan $plan = null): array
    {
        // Ensure plans.duration column is flexible VARCHAR if it is still integer
        try {
            if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'duration')) {
                $colType = Schema::getColumnType('plans', 'duration');
                if (in_array(strtolower($colType), ['integer', 'int', 'bigint', 'smallint', 'tinyint'], true)) {
                    DB::statement("ALTER TABLE `plans` MODIFY COLUMN `duration` VARCHAR(255) NULL");
                }
            }
        } catch (\Throwable) {}

        // Ensure sms_templates.branch_id column exists if table is present
        try {
            if (Schema::hasTable('sms_templates') && !Schema::hasColumn('sms_templates', 'branch_id')) {
                DB::statement("ALTER TABLE `sms_templates` ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `user_id`");
            }
        } catch (\Throwable) {}

        if (!Schema::hasTable('modules') || !Schema::hasTable('user_permissions')) {
            return ['allowed_module_ids' => [], 'synced_modules_count' => 0];
        }

        if (!$plan) {
            $plan = Plan::where('is_active', 1)->latest('id')->first()
                ?: Plan::latest('id')->first();
        }

        $features = $plan ? $plan->features : null;
        $subModules = null;

        $allowedModuleIds = self::resolveAllowedModuleIds($features, $subModules);
        $standardModules = self::getStandardModules();
        $allowedModules = array_filter(
            $standardModules,
            fn($m) => in_array($m['id'], $allowedModuleIds, true)
        );

        $nameCol = Schema::hasColumn('modules', 'name') ? 'name' : 'module';

        // Check if any plan features match existing custom modules in the modules table
        $rawFeatures = [];
        if (!empty($features)) {
            if (is_string($features)) {
                $decoded = json_decode($features, true);
                $rawFeatures = is_array($decoded) ? $decoded : preg_split('/[\n,]+/', $features);
            } elseif (is_array($features)) {
                $rawFeatures = $features;
            }
        }
        if (function_exists('tenant') && ($tMods = tenant('modules'))) {
            if (is_array($tMods)) {
                $rawFeatures = array_merge($rawFeatures, $tMods);
            }
        }

        if (!empty($rawFeatures)) {
            $existingModules = DB::table('modules')->get();
            foreach ($existingModules as $em) {
                $modName = $em->$nameCol ?? '';
                foreach ($rawFeatures as $rf) {
                    if (strcasecmp(trim((string)$modName), trim((string)$rf)) === 0) {
                        $allowedModuleIds[] = (int) $em->id;
                        $allowedModules[] = ['id' => (int) $em->id, 'name' => $modName];
                        break;
                    }
                }
            }
        }
        $allowedModuleIds = array_values(array_unique($allowedModuleIds));

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Throwable) {}

        try {
            DB::statement("SET sql_mode='NO_AUTO_VALUE_ON_ZERO';");
        } catch (\Throwable) {}

        // 1. Sync modules table: insert/update allowed modules, remove unallowed
        foreach ($allowedModules as $mod) {
            DB::table('modules')->updateOrInsert(
                ['id' => $mod['id']],
                [$nameCol => $mod['name'], 'updated_at' => now()]
            );
        }

        if (!empty($allowedModuleIds)) {
            DB::table('modules')->whereNotIn('id', $allowedModuleIds)->delete();
        }

        // 2. Clean up user_permissions table for unallowed modules
        if (!empty($allowedModuleIds)) {
            DB::table('user_permissions')->whereNotIn('module_id', $allowedModuleIds)->delete();
        }

        // 3. Upsert user_permissions ONLY for the client admin user
        $userTable = Schema::hasTable('user') ? 'user' : (Schema::hasTable('users') ? 'users' : null);
        if ($userTable) {
            $adminUser = null;
            if (Schema::hasColumn($userTable, 'role_id')) {
                $adminUser = DB::table($userTable)->where('role_id', 2)->first()
                    ?: DB::table($userTable)->where('id', 1)->first();
            } else {
                $adminUser = DB::table($userTable)->where('role', 'admin')->first()
                    ?: DB::table($userTable)->where('id', 1)->first();
            }

            if ($adminUser) {
                // Delete unallowed modules from user_permissions for admin user
                DB::table('user_permissions')
                    ->where('user_id', $adminUser->id)
                    ->whereNotIn('module_id', $allowedModuleIds)
                    ->delete();

                $permCols = Schema::getColumnListing('user_permissions');

                foreach ($allowedModuleIds as $modId) {
                    $record = [
                        'user_id'    => $adminUser->id,
                        'module_id'  => $modId,
                        'updated_at' => now(),
                    ];
                    if (in_array('create', $permCols, true)) $record['create'] = 1;
                    if (in_array('view', $permCols, true)) $record['view'] = 1;
                    if (in_array('update', $permCols, true)) $record['update'] = 1;
                    if (in_array('delete', $permCols, true)) $record['delete'] = 1;
                    if (in_array('add', $permCols, true)) $record['add'] = 1;
                    if (in_array('edit', $permCols, true)) $record['edit'] = 1;

                    DB::table('user_permissions')->updateOrInsert(
                        [
                            'user_id'   => $adminUser->id,
                            'module_id' => $modId,
                        ],
                        $record
                    );
                }
            }
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable) {}

        return [
            'allowed_module_ids'   => $allowedModuleIds,
            'synced_modules_count' => count($allowedModules),
        ];
    }
}

