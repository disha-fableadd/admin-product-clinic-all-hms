<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class CheckUserLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Step 1: Check if tenant is managed by central admin_erp
        $isTenant = function_exists('tenant') && tenant();
        $tenantUserLimit = $isTenant ? (tenant('user_limit') ?? tenant('max_user')) : null;
        $tenantExpiry = $isTenant ? (tenant('expiry_date') ?? tenant('end_date')) : null;

        // Get plan from local database (or fallback to active plan)
        $plan = null;
        if ($user) {
            if ($user->plan_id) {
                $plan = Plan::find($user->plan_id);
            }
            if (!$plan && $user->created_by) {
                $creator = User::find($user->created_by);
                $plan = $creator ? Plan::find($creator->plan_id) : null;
            }
        }

        if (!$plan) {
            $plan = Plan::where('is_active', 1)->latest('id')->first()
                ?: Plan::latest('id')->first();
        }

        // If user has no plan_id assigned yet but we have a plan, auto-link it
        if ($user && empty($user->plan_id) && $plan) {
            try {
                $user->plan_id = $plan->id;
                $user->save();
            } catch (\Throwable $e) {}
        }

        // Only block if neither central tenant nor local database has any subscription plan
        if (!$isTenant && !$plan) {
            return response()->json([
                'status' => false,
                'message' => 'No subscription plan found for this user.'
            ], 403);
        }

        // Step 2: Check expiration based on tenant expiry or local plan end_date
        $expiration = $tenantExpiry ?: ($plan ? $plan->end_date : null);
        if ($expiration) {
            try {
                $expirationDate = \Carbon\Carbon::parse($expiration)->endOfDay();
                if (now()->greaterThan($expirationDate)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Your subscription plan has expired. Please renew.'
                    ], 403);
                }
            } catch (\Throwable $e) {}
        }

        // Step 3: Enforce staff limit (using admin_erp max_user or local plan user_limit)
        $staffLimit = function_exists('plan_user_limit') ? plan_user_limit() : null;
        if ($staffLimit === null) {
            if ($tenantUserLimit !== null && $tenantUserLimit !== '' && is_numeric($tenantUserLimit)) {
                $staffLimit = (int) $tenantUserLimit;
            } elseif ($plan && !is_null($plan->user_limit) && is_numeric($plan->user_limit)) {
                $staffLimit = (int) $plan->user_limit;
            }
        }

        // null or <= 0 signifies unlimited
        if (!is_null($staffLimit) && $staffLimit > 0) {
            // Count actual staff members (roles other than 1: SuperAdmin, 2: Admin, 5: Patient)
            $currentStaffCount = function_exists('current_staff_count')
                ? current_staff_count()
                : User::where(function ($q) {
                        $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                    })
                    ->whereNotIn('role_id', [1, 2, 5])
                    ->count();

            if ($currentStaffCount >= $staffLimit) {
                return response()->json([
                    'status' => false,
                    'message' => "Maximum staff limit of {$staffLimit} reached for your plan. Please upgrade your plan to add more staff."
                ], 403);
            }
        }

        return $next($request);
    }
}