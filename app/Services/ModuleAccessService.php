<?php

namespace App\Services;

use Illuminate\Http\Request;

class ModuleAccessService
{
    /**
     * Determine if a route or URL is exempt from tenant module access checks.
     */
    public static function isExemptRoute(Request $request, ?string $normalizedRouteName = null): bool
    {
        if (!$normalizedRouteName) {
            $route = $request->route();
            $normalizedRouteName = $route && $route->getName() ? strtolower($route->getName()) : null;
        }

        $exemptRouteNames = [
            'login',
            'login.submit',
            'loginweb',
            'logout',
            'forgot-password',
            'reset-password',
            'profile',
            'profile.edit',
            'clinic.settings.update',
            'tenant.storage',
            'tenant.storage.public',
            'hide.today.appointments',
        ];

        if ($normalizedRouteName && in_array($normalizedRouteName, $exemptRouteNames, true)) {
            return true;
        }

        if ($normalizedRouteName && str_starts_with($normalizedRouteName, 'notification')) {
            return true;
        }

        if ($request->is(
            '/',
            'login',
            'logout',
            'api/login*',
            'api/loginweb',
            'forgot-password',
            'reset-password*',
            'profile*',
            'storage/*',
            'notifications*',
            'all-notifications*'
        )) {
            return true;
        }

        return false;
    }

    /**
     * Resolve the required tenant module name(s) for a given request.
     * Returns null if no specific module restriction applies.
     */
    public static function resolveRequiredModule(Request $request, ?string $normalizedRouteName = null): string|array|null
    {
        if (self::isExemptRoute($request, $normalizedRouteName)) {
            return null;
        }

        if (!$normalizedRouteName) {
            $route = $request->route();
            $normalizedRouteName = $route && $route->getName() ? strtolower($route->getName()) : null;
        }

        // Dashboard
        if ($normalizedRouteName === 'dashboard' || $request->is('dashboard*')) {
            return 'Dashboard';
        }

        // Patients & Reference Doctors
        if ($request->is('patients*', 'diagnosis*', 'referaldoctor*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'patients.') ||
            str_starts_with($normalizedRouteName, 'diagnosis.') ||
            str_starts_with($normalizedRouteName, 'referaldoctor.')
        ))) {
            return 'Patients';
        }

        // Appointments
        if ($request->is('appointment*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'appointment.'))) {
            return 'Appointments';
        }

        // Medicines
        if ($request->is('medicine*', 'category*', 'brand*', 'medicine_unit*', 'symptoms*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'medicine.') ||
            str_starts_with($normalizedRouteName, 'category.') ||
            str_starts_with($normalizedRouteName, 'brand.') ||
            str_starts_with($normalizedRouteName, 'medicine_unit.') ||
            str_starts_with($normalizedRouteName, 'symptoms.')
        ))) {
            return 'Medicines';
        }

        // Followup
        if ($request->is('followup*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'followup.'))) {
            return 'Followup';
        }

        // Staffs
        if ($request->is('user*', 'staff*', 'roles*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'user.') ||
            str_starts_with($normalizedRouteName, 'staff.') ||
            str_starts_with($normalizedRouteName, 'roles.')
        ))) {
            return 'Staffs';
        }

        // Sale Bill
        if ($request->is('sales*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'sales.'))) {
            if ($normalizedRouteName === 'sales.report') {
                return 'Sales Report';
            }
            return 'Sale Bill';
        }

        // Purchase Bill
        if ($request->is('purchase*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'purchase.'))) {
            if ($normalizedRouteName === 'purchase.report') {
                return 'Purchase Report';
            }
            return 'Purchase Bill';
        }

        // Vendors / Suppliers
        if ($request->is('vendor*', 'supplier*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'vendor.') ||
            str_starts_with($normalizedRouteName, 'supplier.')
        ))) {
            return 'Vendors';
        }

        // Treatments & Machines
        if ($request->is('treatment*', 'treatment_booking*', 'machine*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'treatment.') ||
            str_starts_with($normalizedRouteName, 'treatment_booking.') ||
            str_starts_with($normalizedRouteName, 'machine.')
        ))) {
            return 'Treatments';
        }

        // OPD
        if ($request->is('opd_visit*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'opd_visit.'))) {
            return 'OPD';
        }

        // IPD
        if ($request->is('ipd_admit*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'ipd_admit.'))) {
            return 'IPD';
        }

        // Daily Register
        if ($request->is('daily_data*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'daily_data.'))) {
            return 'Daily Register';
        }

        // Therapy
        if ($request->is('therapy*', 'assign-therapy*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'therapy.') ||
            str_starts_with($normalizedRouteName, 'assign-therapy.')
        ))) {
            return 'Therapy';
        }

        // Diet Chart
        if ($request->is('dietchart*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'dietchart.'))) {
            return 'Diet Chart';
        }

        // Clinical Notes
        if ($request->is('assessment*', 'homeadvice*', 'soap*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'assessment.') ||
            str_starts_with($normalizedRouteName, 'homeadvice.') ||
            str_starts_with($normalizedRouteName, 'soap.')
        ))) {
            return 'Clinical Notes';
        }

        // Diagnostic Service
        if ($request->is('service*', 'pathology*', 'pathology_reports*', 'radiology-tests*', 'radiology-reports*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'service.') ||
            str_starts_with($normalizedRouteName, 'pathology.') ||
            str_starts_with($normalizedRouteName, 'pathology_reports.') ||
            str_starts_with($normalizedRouteName, 'radiology-tests.') ||
            str_starts_with($normalizedRouteName, 'radiology-reports.')
        ))) {
            return 'Diagnostic Service';
        }

        // Medical Report
        if ($request->is('report*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'report.'))) {
            return 'Medical Report';
        }

        // OT & Discharge
        if ($request->is('ot*', 'discharge*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'ot.') ||
            str_starts_with($normalizedRouteName, 'discharge.')
        ))) {
            return 'OT & Discharge';
        }

        // Account Ledger
        if ($request->is('account_ledger*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'account_ledger.'))) {
            return 'Account Ledger';
        }

        // Stock / Inventory
        if ($request->is('inventory*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'inventory.'))) {
            return 'Stock / Inventory';
        }

        // Expenses
        if ($request->is('expense*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'expense.'))) {
            if ($normalizedRouteName === 'expense.report') {
                return 'Expense Report';
            }
            return 'Expenses';
        }

        // Invoices
        if ($request->is('invoice*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'invoice.'))) {
            return 'Invoices';
        }

        // Reports
        if ($normalizedRouteName === 'sales.report' || $request->is('sales-report*')) {
            return 'Sales Report';
        }
        if ($normalizedRouteName === 'purchase.report' || $request->is('purchase-report*')) {
            return 'Purchase Report';
        }
        if ($normalizedRouteName === 'expense.report' || $request->is('expense-report*')) {
            return 'Expense Report';
        }
        if ($normalizedRouteName === 'chart' || $request->is('chart*')) {
            return 'Analytics & Summary';
        }

        // Settings
        if ($request->is('setting*', 'settings*', 'email*', 'sms*') || ($normalizedRouteName && (
            str_starts_with($normalizedRouteName, 'setting.') ||
            str_starts_with($normalizedRouteName, 'settings.') ||
            str_starts_with($normalizedRouteName, 'email.') ||
            str_starts_with($normalizedRouteName, 'sms.')
        ))) {
            return 'Settings';
        }

        // Tax Rates
        if ($request->is('taxrate*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'taxrate.'))) {
            return 'Tax Rates';
        }

        // My Plan Details
        if ($request->is('plans*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'plans.'))) {
            return 'My Plan Details';
        }

        // Calendar
        if ($request->is('calender*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'calender.'))) {
            return 'Calendar';
        }

        // Knowledge Base
        if ($request->is('knowledge*') || ($normalizedRouteName && str_starts_with($normalizedRouteName, 'knowledge.'))) {
            return 'Knowledge Base';
        }

        return null;
    }
}

