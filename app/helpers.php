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
        if (! config('tenant.enabled') || ! tenant()) {
            return true;
        }

        $assigned = (array) tenant('modules', []);

        if (empty($assigned)) {
            return false;
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
            return asset('admin/assets/img/cliniclogo.png');
        }

        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            return $relativePath;
        }

        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        if (config('tenant.enabled') && tenant('id')) {
            return url('storage/tenants/' . tenant('id') . '/app/public/' . $relativePath);
        }

        return url('storage/' . $relativePath);
    }
}

