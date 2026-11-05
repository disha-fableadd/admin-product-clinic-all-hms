<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                $branches = Branch::all();
            } catch (\Throwable $e) {
                $branches = collect(); // avoid crash if table missing
            }

            $view->with('allBranches', $branches);
        });

        $settings = [];
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->toArray();
            }
        } catch (\Throwable $e) {
            $settings = [];
        }

        $mailConfigMap = [
            'mail_host' => 'mail.mailers.smtp.host',
            'mail_port' => 'mail.mailers.smtp.port',
            'mail_username' => 'mail.mailers.smtp.username',
            'mail_password' => 'mail.mailers.smtp.password',
            'mail_encryption' => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name' => 'mail.from.name',
        ];

        foreach ($mailConfigMap as $settingKey => $configKey) {
            if (array_key_exists($settingKey, $settings) && $settings[$settingKey] !== null && $settings[$settingKey] !== '') {
                Config::set($configKey, $settings[$settingKey]);
            }
        }

        $this->app->singleton('hasPermission', function () {
            return function ($moduleId, $action) {
                // Map numeric module IDs to module names for tenant module access validation
                static $idToNameMap = [
                    1  => 'Medicines',
                    2  => 'Followup',
                    3  => 'Staffs',
                    4  => 'Medicines',
                    5  => 'Patients',
                    6  => 'Appointments',
                    7  => 'Treatments',
                    8  => 'Diagnostic Services',
                    9  => 'Treatments',
                    10 => 'Treatments',
                    11 => 'OPD',
                    12 => 'Vendors',
                    13 => 'IPD',
                    14 => 'Daily Register',
                    15 => 'OT & Discharge',
                    16 => 'OT & Discharge',
                    17 => 'Diagnostic Service',
                    18 => 'Diagnostic Service',
                    19 => 'Diagnostic Service',
                    20 => 'Sale Bill',
                    21 => 'Purchase Bill',
                    22 => 'Expenses',
                    23 => 'Stock / Inventory',
                    24 => 'Vendors',
                    25 => 'Medical Report',
                    26 => 'Invoices',
                    27 => 'Clinical Notes',
                    28 => 'Clinical Notes',
                    29 => 'Therapy',
                    30 => 'Branch',
                    31 => 'Account Ledger',
                    32 => 'Analytics & Summary',
                    33 => 'Clinical Notes',
                    34 => 'Diet Chart',
                    35 => 'Tax Rates',
                    36 => 'Patients',
                    37 => 'Sales Report',
                    38 => 'Purchase Report',
                    39 => 'Expense Report',
                    40 => 'Analytics & Summary',
                ];

                // If tenancy is active, enforce plan module restrictions for ALL users (including Admin)
                if (function_exists('tenant_has_module')) {
                    if ($moduleId === 1) {
                        if (!tenant_has_module('Medicines') && !tenant_has_module('Dashboard')) {
                            return false;
                        }
                    } elseif ($moduleId === 4) {
                        if (!tenant_has_module('Settings') && !tenant_has_module('Medicines')) {
                            return false;
                        }
                    } elseif ($moduleId === 21) {
                        if (!tenant_has_module('Purchase Bill') && !tenant_has_module('Tax Rates') && !tenant_has_module('Diagnostic Service')) {
                            return false;
                        }
                    } elseif ($moduleId === 35) {
                        if (!tenant_has_module('Patients') && !tenant_has_module('Tax Rates')) {
                            return false;
                        }
                    } elseif (isset($idToNameMap[$moduleId]) && !tenant_has_module($idToNameMap[$moduleId])) {
                        return false;
                    }
                }

                $user = Auth::user();
                if (!$user) {
                    return false;
                }

                // Admins have full access to tenant-permitted modules
                if ((string)$user->role_id === '2' || (int)$user->role_id === 1) {
                    return true;
                }

                $actionNormalized = match(strtolower((string)$action)) {
                    'add' => 'create',
                    'edit' => 'update',
                    default => strtolower((string)$action),
                };

                if (isset($user->permissions) && is_iterable($user->permissions)) {
                    $permission = collect($user->permissions)->firstWhere('module_id', $moduleId);
                    if ($permission) {
                        return !empty($permission[$actionNormalized]) || !empty($permission[$action]);
                    }
                }

                return \App\Models\UserPermission::where('user_id', $user->id)
                    ->where('module_id', $moduleId)
                    ->where(function($q) use ($actionNormalized, $action) {
                        $q->where($actionNormalized, 1)
                          ->orWhere($action, 1);
                    })
                    ->exists();
            };
        });

    }
}
