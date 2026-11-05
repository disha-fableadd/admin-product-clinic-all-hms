<?php

if (! function_exists('tenant')) {
    /**
     * Current client (tenant) details from admin_erp, or null when tenancy is off.
     *
     * @return mixed
     */
    function tenant(?string $key = null, $default = null)
    {
        $tenant = app(\App\Services\Tenancy\TenantManager::class)->current();

        return $key === null ? $tenant : data_get($tenant, $key, $default);
    }
}

if (! function_exists('tenant_has_module')) {
    /**
     * Check if current tenant has access to given module name(s).
     * If tenancy is disabled or not active, returns true.
     * Automatically handles common module aliases.
     *
     * @param string|array $moduleNames
     * @return bool
     */
    function tenant_has_module($moduleNames): bool
    {
        $assigned = [];
        if (function_exists('tenant') && tenant()) {
            $assigned = (array) tenant('modules', []);
        }

        // If not in tenant session, fallback to active Plan features from database (matching Hotel ERP behavior)
        if (empty($assigned)) {
            try {
                $dbPlanFeatures = null;
                $user = auth()->user();
                if ($user && method_exists($user, 'getActivePlan')) {
                    $p = $user->getActivePlan();
                    if ($p && !empty($p->features)) {
                        $dbPlanFeatures = $p->features;
                    }
                }
                if (!$dbPlanFeatures) {
                    $activePlan = \App\Models\Plan::where('is_active', 1)->latest('id')->first()
                        ?: \App\Models\Plan::latest('id')->first();
                    if ($activePlan && !empty($activePlan->features)) {
                        $dbPlanFeatures = $activePlan->features;
                    }
                }
                if (!empty($dbPlanFeatures)) {
                    $assigned = is_array($dbPlanFeatures) ? $dbPlanFeatures : (json_decode($dbPlanFeatures, true) ?: []);
                }
                if (empty($assigned) && \Illuminate\Support\Facades\Schema::hasTable('modules')) {
                    $nameCol = \Illuminate\Support\Facades\Schema::hasColumn('modules', 'name') ? 'name' : 'module';
                    $assigned = \Illuminate\Support\Facades\DB::table('modules')->pluck($nameCol)->all();
                }
            } catch (\Throwable $e) {}
        }

        if (empty($assigned)) {
            return !config('tenant.enabled');
        }

        $assignedNormalized = array_map(function ($m) {
            return strtolower(trim((string) $m));
        }, $assigned);

        static $aliasGroups = [
            ['patients', 'all patients', 'patient'],
            ['appointments', 'appointment', 'all appointments'],
            ['medicines', 'medicine', 'all medicines', 'pharmacy'],
            ['followup', 'followups', 'follow up', 'all followup'],
            ['staffs', 'staff', 'employees', 'employee', 'all staffs'],
            ['sale bill', 'sale bills', 'sales', 'sales & bills', 'sales and bills'],
            ['purchase bill', 'purchase bills', 'purchases', 'purchase'],
            ['vendors', 'vendor', 'suppliers', 'supplier'],
            ['treatments', 'treatment', 'all treatments'],
            ['opd', 'opd visit', 'all opd'],
            ['ipd', 'ipd admit', 'all ipd'],
            ['daily register', 'daily data', 'all daily register'],
            ['therapy', 'therapies', 'assign therapy', 'all therapy'],
            ['diet chart', 'diet charts', 'all diet charts'],
            ['clinical notes', 'notes', 'assessment', 'home advice', 'soap'],
            ['diagnostic service', 'diagnostic services', 'pathology', 'radiology', 'service master'],
            ['medical report', 'medical reports', 'all medical reports'],
            ['ot & discharge', 'ot procedure', 'discharge'],
            ['account ledger', 'ledger', 'accounting'],
            ['stock / inventory', 'stock', 'inventory', 'manage inventory'],
            ['expenses', 'expense', 'all expenses'],
            ['invoices', 'invoice'],
            ['sales report'],
            ['purchase report'],
            ['expense report'],
            ['analytics & summary', 'analytics', 'charts', 'chart'],
            ['settings', 'setting'],
            ['tax rates', 'tax rate', 'tax'],
            ['my plan details', 'plans', 'membership'],
            ['calendar', 'calender'],
            ['knowledge base', 'knowledge'],
            ['dashboard'],
        ];

        $checkList = is_array($moduleNames) ? $moduleNames : [$moduleNames];
        $expandedCheckList = [];

        foreach ($checkList as $name) {
            $norm = strtolower(trim((string) $name));
            $expandedCheckList[$norm] = true;
            foreach ($aliasGroups as $group) {
                if (in_array($norm, $group, true)) {
                    foreach ($group as $alias) {
                        $expandedCheckList[$alias] = true;
                    }
                }
            }
        }

        foreach (array_keys($expandedCheckList) as $name) {
            if (in_array($name, $assignedNormalized, true)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('tenant_has_sub_module')) {
    /**
     * Check if current tenant has access to given sub-module name(s) / slug(s).
     * If $parentModuleName is given, checks if tenant has the parent module first.
     * If tenancy is disabled or not active, returns true.
     *
     * @param string|array $subModuleNames
     * @param string|array|null $parentModuleName
     * @return bool
     */
    function tenant_has_sub_module($subModuleNames, $parentModuleName = null): bool
    {
        if (! config('tenant.enabled') || ! tenant()) {
            return true;
        }

        if ($parentModuleName !== null && ! tenant_has_module($parentModuleName)) {
            return false;
        }

        $rawNames = tenant('sub_module_names') ?: tenant('sub_modules') ?: [];
        $activeNames = array_map(function ($m) {
            return strtolower(trim((string) $m));
        }, (array) $rawNames);
        $activeSlugs = array_map('strtolower', (array) tenant('sub_module_slugs', []));

        // If no sub-modules are tracked at all in tenant payload, default to parent module status
        if (empty($activeNames) && empty($activeSlugs)) {
            return $parentModuleName ? tenant_has_module($parentModuleName) : true;
        }

        $checkList = is_array($subModuleNames) ? $subModuleNames : [$subModuleNames];
        foreach ($checkList as $item) {
            $normalized = strtolower(trim((string) $item));
            if (in_array($normalized, $activeNames, true) || in_array($normalized, $activeSlugs, true)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('can_access_module')) {
    /**
     * Check if a module or feature is accessible by tenant plan and user permission.
     *
     * @param string|array $moduleNames
     * @param string|array|null $subModuleNames
     * @param string|array|null $parentModuleName
     * @param int|null $permissionModuleId
     * @param string $action 'view'|'create'|'edit'|'delete'
     * @return bool
     */
    function can_access_module($moduleNames, $subModuleNames = null, $parentModuleName = null, ?int $permissionModuleId = null, string $action = 'view'): bool
    {
        // 1. Tenant module check (if tenancy active)
        if (config('tenant.enabled') && tenant()) {
            $hasTenantAccess = false;
            if (! empty($moduleNames) && tenant_has_module($moduleNames)) {
                $hasTenantAccess = true;
            }
            if (! $hasTenantAccess && ! empty($subModuleNames)) {
                if (tenant_has_sub_module($subModuleNames, $parentModuleName)) {
                    $hasTenantAccess = true;
                }
            }
            if (! $hasTenantAccess) {
                return false;
            }
        }

        // 2. User permission check (if permissionModuleId specified)
        if ($permissionModuleId !== null && function_exists('app')) {
            try {
                $hasPerm = app('hasPermission');
                if (is_callable($hasPerm)) {
                    return (bool) $hasPerm($permissionModuleId, $action);
                }
            } catch (\Throwable) {
                return true;
            }
        }

        return true;
    }
}

if (! function_exists('tenant_image_url')) {
    /**
     * Build an accessible URL for a storage image.
     */
    function tenant_image_url(?string $relativePath): string
    {
        $relativePath = ltrim(trim((string) $relativePath), '/');
        if ($relativePath === '') {
            return hms_default_logo();
        }

        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            return $relativePath;
        }

        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        if (config('tenant.enabled') && tenant('id')) {
            return url('storage/tenants/' . tenant('id') . '/app/public/' . $relativePath);
        }

        return url('public/storage/' . $relativePath);
    }
}

if (! function_exists('hms_default_logo')) {
    /**
     * Default application/clinic logo URL on live server and local.
     */
    function hms_default_logo(): string
    {
        return url('public/admin/assets/img/Fablead_HMS_4K.png');
    }
}

if (! function_exists('hms_default_profile')) {
    /**
     * Default user avatar image URL on live server and local.
     */
    function hms_default_profile(): string
    {
        return url('public/admin/assets/img/images.png');
    }
}

if (! function_exists('hms_clinic_logo')) {
    /**
     * Active clinic logo: returns custom uploaded logo if present, else default Fablead logo.
     */
    function hms_clinic_logo(): string
    {
        try {
            $custom = \App\Models\Setting::where('key', 'clinic_logo')->value('value');
            if (!empty($custom) && !in_array($custom, ['default-logo.png', 'admin/assets/img/cliniclogo.png', 'admin/assets/img/Fablead_HMS_4K.png'], true)) {
                if (str_starts_with($custom, 'http://') || str_starts_with($custom, 'https://')) {
                    return $custom;
                }
                $clean = ltrim($custom, '/');
                if (str_starts_with($clean, 'public/')) {
                    return url($clean);
                }
                return url('public/' . $clean);
            }
        } catch (\Throwable $e) {}

        return hms_default_logo();
    }
}

if (! function_exists('hms_user_profile')) {
    /**
     * User profile avatar URL: returns custom uploaded avatar if present, else default images.png.
     */
    function hms_user_profile($user = null): string
    {
        try {
            $u = $user ?: auth()->user();
            if ($u) {
                // If model has getProfileAttribute, raw value or accessor
                $raw = $u->getRawOriginal('profile') ?? ($u->profile ?? null);
                if (!empty($raw) && !in_array($raw, ['default.png', 'default.webp', 'img1.png', 'images.png', 'admin/assets/img/images.png', 'admin/assets/img/img1.png'], true)) {
                    if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                        return $raw;
                    }
                    $clean = ltrim($raw, '/');
                    if (str_starts_with($clean, 'public/')) {
                        return url($clean);
                    }
                    if (!str_starts_with($clean, 'profile/') && !str_starts_with($clean, 'uploads/')) {
                        $clean = 'profile/' . $clean;
                    }
                    return url('public/' . $clean);
                }
            }
        } catch (\Throwable $e) {}

        return hms_default_profile();
    }
}


