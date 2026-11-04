<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->get();
        return view('plans.planlist', compact('plans'));
    }

    public function create()
    {
        return view('plans.addplan');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        // Process features
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        DB::transaction(function () use ($validated, $featuresArray, $request) {
            $plan = Plan::create([
                'name'          => $validated['name'],
                'price'         => $validated['price'],
                'start_date'    => $validated['start_date'],
                'end_date'      => $validated['end_date'],
                'duration'      => $request->duration,
                'total_amount'  => $request->total_amount,
                'subtitle'      => $validated['subtitle'],
                'user_limit'    => $validated['user_limit'],
                'branch_limit'  => $validated['branch_limit'],
                'storage_limit' => $validated['storage_limit'],
                'is_active'     => $validated['is_active'],
                'features'      => $featuresArray,
            ]);
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('plans.editplan', compact('plan'));
    }

    public function show(Plan $plan)
    {
        return view('plans.viewplan', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $this->validatePlan($request);

        // Process features
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        DB::transaction(function () use ($validated, $featuresArray, $request, $plan) {
            $updateData = [
                'name'          => $validated['name'],
                'price'         => $validated['price'] ?? null,
                'start_date'    => $validated['start_date'] ?? null,
                'end_date'      => $validated['end_date'] ?? null,
                'subtitle'      => $validated['subtitle'] ?? null,
                'user_limit'    => $validated['user_limit'] ?? null,
                'branch_limit'  => $validated['branch_limit'] ?? null,
                'storage_limit' => $validated['storage_limit'] ?? null,
                'is_active'     => $validated['is_active'],
                'features'      => $featuresArray,
            ];
            $plan->update($updateData);
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        DB::transaction(function () use ($plan) {
            $plan->delete();
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan deleted successfully.');
    }

    public function myplan()
    {
        $user = auth()->user();
        $plan = $user ? $user->getActivePlan() : null;

        if (!$plan) {
            $plan = Plan::where('is_active', 1)->latest('id')->first() ?? Plan::latest('id')->first();
        }

        if (!$plan) {
            $plan = Plan::create([
                'name' => 'Starter Plan',
                'price' => 0,
                'total_amount' => 0,
                'duration' => 'year',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'is_active' => 1,
                'user_limit' => 5,
                'branch_limit' => 1,
                'features' => ['Dashboard', 'Patients', 'Appointments', 'Medicines', 'Staffs']
            ]);
        }

        $tenant = function_exists('tenant') ? tenant() : null;
        $clientId = $tenant['client_id'] ?? ($tenant['id'] ?? null);
        $apiUrl = config('tenant.api_url');
        $superAdminBase = preg_replace('#/api/tenant/?$#i', '', $apiUrl);

        // Sync latest validated subscription dates from Super Admin tenant payload if available
        if ($tenant && !empty($tenant['expiry_date']) && $plan) {
            $tenantExpiry = Carbon::parse($tenant['expiry_date'])->format('Y-m-d');
            $tenantStart = !empty($tenant['start_date']) ? Carbon::parse($tenant['start_date'])->format('Y-m-d') : null;

            $needsUpdate = false;
            $updateData = [];

            if ($plan->end_date !== $tenantExpiry) {
                $updateData['end_date'] = $tenantExpiry;
                $needsUpdate = true;
            }
            if ($tenantStart && $plan->start_date !== $tenantStart) {
                $updateData['start_date'] = $tenantStart;
                $needsUpdate = true;
            }
            if (!empty($tenant['billing_cycle']) && $plan->duration !== (str_contains(strtolower($tenant['billing_cycle']), 'month') ? 'month' : 'year')) {
                $updateData['duration'] = str_contains(strtolower($tenant['billing_cycle']), 'month') ? 'month' : 'year';
                $needsUpdate = true;
            }

            $tenantUserLimit = $tenant['user_limit'] ?? ($tenant['max_user'] ?? null);
            $tenantBranchLimit = $tenant['branch_limit'] ?? ($tenant['max_branch'] ?? null);

            if ($tenantUserLimit !== null && (int)$plan->user_limit !== (int)$tenantUserLimit) {
                $updateData['user_limit'] = (int)$tenantUserLimit;
                $needsUpdate = true;
            }
            if ($tenantBranchLimit !== null && (int)$plan->branch_limit !== (int)$tenantBranchLimit) {
                $updateData['branch_limit'] = (int)$tenantBranchLimit;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $plan->update($updateData);
                $plan->refresh();
            }
        }

        $paymentHistory = [];

        // Fetch official transactions/payments from Super Admin for this client
        if ($clientId) {
            try {
                $resp = Http::timeout(6)->withHeaders([
                    'X-Tenant-Secret' => config('tenant.secret'),
                ])->post("{$superAdminBase}/api/tenant/billing-history", [
                    'client_id' => $clientId,
                ]);

                if ($resp->successful() && !empty($resp->json('data'))) {
                    $paymentHistory = $resp->json('data');
                }
            } catch (\Throwable $e) {
                Log::warning('Failed fetching billing history from Super Admin: ' . $e->getMessage());
            }
        }

        $planHistory = [];
        if (empty($paymentHistory)) {
            $planHistory = Plan::latest('id')->get();
        }

        return view('plans.myplan', compact('user', 'plan', 'planHistory', 'paymentHistory'));
    }

    public function myPlanDetails()
    {
        $user = auth()->user();
        $plan = $user ? $user->getActivePlan() : null;
        if (!$plan) {
            $plan = Plan::where('is_active', 1)->latest('id')->first();
        }

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'tenant' => function_exists('tenant') ? tenant() : null,
        ]);
    }

    /**
     * Get available upgrade plans from Super Admin (or local plans fallback).
     */
    public function getUpgradePlans(Request $request)
    {
        $tenant = function_exists('tenant') ? tenant() : null;
        $apiUrl = config('tenant.api_url');
        $superAdminBase = preg_replace('#/api/tenant/?$#i', '', $apiUrl);
        $memberships = [];

        try {
            $resp = Http::timeout(6)->get("{$superAdminBase}/api/memberships", [
                'status' => 'Active',
            ]);

            if ($resp->successful() && !empty($resp->json('data'))) {
                $memberships = $resp->json('data');
            }
        } catch (\Throwable $e) {
            Log::warning('Failed fetching memberships from Super Admin: ' . $e->getMessage());
        }

        $currentPlan = auth()->user() ? auth()->user()->getActivePlan() : null;

        return response()->json([
            'success' => true,
            'current_plan' => $currentPlan,
            'tenant' => $tenant,
            'available_plans' => $memberships,
        ]);
    }

    /**
     * Create Razorpay order via Super Admin's Razorpay API.
     */
    public function createRazorpayOrder(Request $request)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:renewal,upgrade',
            'amount' => 'required|numeric|min:1',
            'membership_id' => 'nullable|integer',
            'billing_cycle' => 'nullable|string',
        ]);

        $tenant = function_exists('tenant') ? tenant() : null;
        $clientId = $tenant['client_id'] ?? ($tenant['id'] ?? null);
        $apiUrl = config('tenant.api_url');
        $superAdminBase = preg_replace('#/api/tenant/?$#i', '', $apiUrl);

        $user = auth()->user();
        $payload = [
            'amount' => (float) $validated['amount'],
            'client_id' => $clientId,
            'payment_type' => $validated['payment_type'],
            'name' => $tenant['client_name'] ?? ($user->fullname ?? 'HMS Client'),
            'email' => $user->email ?? ($tenant['email'] ?? ''),
            'contact' => $user->phone ?? ($tenant['phone'] ?? ''),
            'description' => $validated['payment_type'] === 'renewal' 
                ? 'HMS Plan renewal payment for ' . ($tenant['client_name'] ?? ($user->fullname ?? 'HMS Client'))
                : 'HMS Plan upgrade payment for ' . ($tenant['client_name'] ?? ($user->fullname ?? 'HMS Client')),
        ];

        if ($validated['payment_type'] === 'upgrade' && !empty($validated['membership_id'])) {
            $payload['membership_id'] = $validated['membership_id'];
            $payload['billing_cycle'] = $validated['billing_cycle'] ?? 'yearly';
        }

        try {
            $resp = Http::timeout(10)->post("{$superAdminBase}/api/razorpay/create-order", $payload);

            if ($resp->successful() && !empty($resp->json('data'))) {
                return response()->json([
                    'success' => true,
                    'order' => $resp->json('data'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $resp->json('message') ?? 'Failed to initialize payment gateway order.',
                'error' => $resp->json(),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to connect to Super Admin payment gateway: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment with Super Admin and synchronously update client's local DB.
     */
    public function verifyRazorpayPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'payment_type' => 'required|in:renewal,upgrade',
            'amount' => 'required|numeric',
            'membership_id' => 'nullable|integer',
            'billing_cycle' => 'nullable|string',
        ]);

        $tenant = function_exists('tenant') ? tenant() : null;
        $clientId = $tenant['client_id'] ?? ($tenant['id'] ?? null);
        $apiUrl = config('tenant.api_url');
        $superAdminBase = preg_replace('#/api/tenant/?$#i', '', $apiUrl);

        try {
            $verifyResp = Http::timeout(12)->post("{$superAdminBase}/api/razorpay/verify-payment", [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
                'client_id' => $clientId,
                'payment_type' => $validated['payment_type'],
                'amount' => $validated['amount'],
                'billing_cycle' => $validated['billing_cycle'] ?? null,
            ]);

            if (!$verifyResp->successful() || !$verifyResp->json('success')) {
                Log::warning('Super Admin Razorpay verification rejected', [
                    'status' => $verifyResp->status(),
                    'response' => $verifyResp->json() ?? $verifyResp->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $verifyResp->json('message') ?? 'Payment verification failed at Super Admin.',
                ], 400);
            }

            if ($tenant && !empty($tenant['domain'])) {
                app(\App\Services\Tenancy\TenantManager::class)->forgetCachedHost($tenant['domain']);
            }

            $currentPlan = auth()->user() ? auth()->user()->getActivePlan() : Plan::latest('id')->first();
            $now = Carbon::now();

            if ($validated['payment_type'] === 'renewal') {
                $cycle = $currentPlan ? strtolower(trim((string)$currentPlan->duration)) : 'year';
                if (!empty($validated['billing_cycle'])) {
                    $cycle = strtolower(trim((string)$validated['billing_cycle']));
                }

                $today = Carbon::today();
                $currentEndDate = ($currentPlan && !empty($currentPlan->end_date)) 
                    ? Carbon::parse($currentPlan->end_date)->startOfDay() 
                    : null;

                $isActivePlan = ($currentEndDate && $currentEndDate->greaterThanOrEqualTo($today));
                $renewalStartDate = $isActivePlan ? $currentEndDate->toDateString() : $today->toDateString();
                $baseDate = Carbon::parse($renewalStartDate);
                $newEndDate = (str_contains($cycle, 'month')) 
                    ? $baseDate->copy()->addMonth()->toDateString() 
                    : $baseDate->copy()->addYear()->toDateString();

                if ($currentPlan) {
                    $durationVal = str_contains($cycle, 'month') ? 'month' : 'year';
                    $planUpdatePayload = [
                        'is_active' => 1,
                        'duration' => $durationVal,
                        'start_date' => $renewalStartDate,
                        'end_date' => $newEndDate,
                        'total_amount' => $validated['amount'],
                    ];

                    try {
                        DB::statement("ALTER TABLE `plans` MODIFY COLUMN `duration` VARCHAR(255) NULL");
                        $currentPlan->update($planUpdatePayload);
                    } catch (\Illuminate\Database\QueryException $qe) {
                        if (str_contains($qe->getMessage(), 'duration')) {
                            try {
                                DB::statement("ALTER TABLE `plans` MODIFY COLUMN `duration` VARCHAR(255) NULL");
                                $currentPlan->update($planUpdatePayload);
                            } catch (\Throwable $e2) {
                                $planUpdatePayload['duration'] = str_contains($cycle, 'month') ? 1 : 12;
                                $currentPlan->update($planUpdatePayload);
                            }
                        } else {
                            throw $qe;
                        }
                    }

                    if (auth()->check()) {
                        auth()->user()->update(['plan_id' => $currentPlan->id]);
                    }
                }
            } elseif ($validated['payment_type'] === 'upgrade' && !empty($validated['membership_id'])) {
                $membershipResp = Http::timeout(6)->get("{$superAdminBase}/api/memberships/{$validated['membership_id']}");
                $membershipData = $membershipResp->successful() ? $membershipResp->json('data') : null;

                $planName = $membershipData['plan_name'] ?? 'Upgraded Plan';
                $features = [];
                if (!empty($membershipData['project_modules_data'])) {
                    $features = array_column($membershipData['project_modules_data'], 'name');
                }

                $cycle = strtolower(trim((string) ($validated['billing_cycle'] ?? 'year')));
                $newEndDate = (str_contains($cycle, 'month'))
                    ? $now->copy()->addMonth()->format('Y-m-d')
                    : $now->copy()->addYear()->format('Y-m-d');

                Plan::where('is_active', 1)->update(['is_active' => 0]);

                $newPlan = Plan::create([
                    'name' => $planName,
                    'price' => $validated['amount'],
                    'total_amount' => $validated['amount'],
                    'duration' => (str_contains($cycle, 'month')) ? 'month' : 'year',
                    'start_date' => $now->format('Y-m-d'),
                    'end_date' => $newEndDate,
                    'subtitle' => 'Upgraded via Razorpay Checkout',
                    'user_limit' => $membershipData['max_user'] ?? null,
                    'branch_limit' => $membershipData['max_branch'] ?? null,
                    'is_active' => 1,
                    'features' => $features,
                ]);

                if (auth()->check()) {
                    auth()->user()->update(['plan_id' => $newPlan->id]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and plan updated successfully!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during payment processing: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Tax Invoice / Receipt PDF for a subscription plan matching HMS design.
     */
    public function downloadInvoicePdf($id = null)
    {
        $user = auth()->user();
        $plan = $user ? $user->getActivePlan() : Plan::latest('id')->first();

        if (!$plan) {
            return redirect()->back()->with('error', 'Plan not found.');
        }

        $apiUrl = config('tenant.api_url');
        $superAdminBase = preg_replace('#/api/tenant/?$#i', '', $apiUrl);
        $tenant = function_exists('tenant') ? (tenant() ?? []) : [];
        $productId = $tenant['product_id'] ?? null;
        $projectId = $tenant['project_id'] ?? null;
        $clientId = $tenant['client_id'] ?? ($tenant['id'] ?? null);

        $companyDetails = [];
        $termsList = [];

        try {
            $resp = Http::timeout(5)->get($superAdminBase . '/api/invoice-details', [
                'product_id' => $productId,
                'project_id' => $projectId,
            ]);
            if ($resp->successful() && !empty($resp->json('data'))) {
                $data = $resp->json('data');
                $companyDetails = $data['company_details'] ?? [];
                $termsList = $data['terms'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::warning('Invoice details fetch error: ' . $e->getMessage());
        }

        $transactionId = request('transaction_id') ?: ($id && is_numeric($id) && $id > 10 ? $id : null);
        $transactionData = null;

        if ($clientId && $transactionId) {
            try {
                $txResp = Http::timeout(5)->withHeaders([
                    'X-Tenant-Secret' => config('tenant.secret'),
                ])->post("{$superAdminBase}/api/tenant/billing-history", [
                    'client_id' => $clientId,
                    'transaction_id' => $transactionId,
                ]);

                if ($txResp->successful() && !empty($txResp->json('data'))) {
                    $transactionData = $txResp->json('data')[0] ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed fetching specific transaction: ' . $e->getMessage());
            }
        }

        if (empty($companyDetails['company_name'])) {
            $companyDetails['company_name'] = 'FABLEAD DEVELOPERS TECHNOLAB';
        }
        if (empty($companyDetails['address'])) {
            $companyDetails['address'] = 'Ascon Plaza, A/5001, nr. bhulka bhavan school, Adajan Gam, Adajan, Surat, Gujarat 395009';
        }
        if (empty($companyDetails['contact'])) {
            $companyDetails['contact'] = '+91 94295 26494';
        }
        if (empty($companyDetails['email'])) {
            $companyDetails['email'] = 'info@fableadtechnolabs.com';
        }

        if (empty($termsList)) {
            $termsList = [
                'This invoice is valid for the period mentioned in the order details.',
                'Payment is non-refundable once the service is activated.',
                'Renewal will be applicable as per the mentioned amount and validity.',
                'For any support or query, please contact us at the provided email or phone number.',
            ];
        }

        $customerName = $tenant['client_name'] ?? ($user->fullname ?? 'HMS Client');
        $companyName = $tenant['brand_name'] ?? ($tenant['client_name'] ?? ($user->clinic_name ?? $customerName));
        $customerPhone = $user->phone ?? ($tenant['phone'] ?? '');
        $customerEmail = $user->email ?? ($tenant['email'] ?? '');

        $invoiceYear = date('Y');
        $invoiceNo = $transactionData && !empty($transactionData['id'])
            ? 'INV-' . Carbon::parse($transactionData['created_at'])->format('Y') . '-' . str_pad((string)$transactionData['id'], 4, '0', STR_PAD_LEFT)
            : 'INV-' . $invoiceYear . '-' . str_pad((string)$plan->id, 4, '0', STR_PAD_LEFT);

        $orderId = $transactionData['razorpay_order_id'] ?? ($transactionData['id'] ?? (string)$plan->id);
        $orderDate = $transactionData && !empty($transactionData['created_at'])
            ? Carbon::parse($transactionData['created_at'])->format('d M Y')
            : ($plan->start_date ? Carbon::parse($plan->start_date)->format('d M Y') : date('d M Y'));

        $paymentMethod = $transactionData['payment_method'] ?? 'Razorpay';
        $paymentStatus = 'Completed (Paid)';

        if ($transactionData) {
            $plan = clone $plan;
            if (!empty($transactionData['amount'])) {
                $plan->total_amount = (float) $transactionData['amount'];
                $plan->price = (float) $transactionData['amount'];
            }
            if (!empty($transactionData['plan_name'])) {
                $plan->name = $transactionData['plan_name'];
            }
            if (!empty($transactionData['billing_cycle'])) {
                $plan->duration = $transactionData['billing_cycle'];
            }
            if (!empty($transactionData['period_start'])) {
                $plan->start_date = $transactionData['period_start'];
            }
            if (!empty($transactionData['period_end'])) {
                $plan->end_date = $transactionData['period_end'];
            }
            if (!empty($transactionData['payment_type'])) {
                $plan->subtitle = ucfirst($transactionData['payment_type']) . ' Payment';
            }
        }

        $validity = ($plan->start_date ? Carbon::parse($plan->start_date)->format('Y-m-d') : date('Y-m-d')) . ' to ' . ($plan->end_date ? Carbon::parse($plan->end_date)->format('Y-m-d') : date('Y-m-d'));

        $featuredModules = [];
        if (!empty($tenant['modules']) && is_array($tenant['modules'])) {
            $featuredModules = array_values($tenant['modules']);
        } elseif (!empty($plan->features) && is_array($plan->features)) {
            $featuredModules = array_values($plan->features);
        } else {
            $featuredModules = ['Dashboard', 'Patients', 'Appointments', 'Medicines', 'Staffs', 'Treatments', 'Clinical Services', 'Billing'];
        }

        $productTitle = $tenant['product_code'] ?? 'HMS';

        $pdfData = [
            'company' => $companyDetails,
            'customer' => [
                'name' => $customerName,
                'company' => $companyName,
                'phone' => $customerPhone,
                'email' => $customerEmail,
            ],
            'order' => [
                'status' => $paymentStatus,
                'invoice_no' => $invoiceNo,
                'order_id' => $orderId,
                'date' => $orderDate,
                'payment_method' => $paymentMethod,
                'validity' => $validity,
            ],
            'plan' => $plan,
            'product_title' => $productTitle,
            'featured_modules' => $featuredModules,
            'terms' => $termsList,
        ];

        $pdf = Pdf::loadView('plans.invoice_pdf', $pdfData)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Invoice_' . $invoiceNo . '.pdf');
    }

    // API Methods for AJAX calls
    public function apiList()
    {
        $plans = Plan::latest()->get();
        return response()->json(['plans' => $plans]);
    }

    public function apiShow($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        return response()->json(['plan' => $plan]);
    }

    public function apiStore(Request $request)
    {
        $validated = $this->validatePlan($request);
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        try {
            $plan = DB::transaction(function () use ($validated, $featuresArray, $request) {
                return Plan::create([
                    'name'          => $validated['name'],
                    'price'         => $validated['price'] ?? null,
                    'start_date'    => $validated['start_date'] ?? null,
                    'end_date'      => $validated['end_date'] ?? null,
                    'duration'      => $request->duration,
                    'total_amount'  => $request->total_amount,
                    'subtitle'      => $validated['subtitle'] ?? null,
                    'user_limit'    => $validated['user_limit'] ?? null,
                    'branch_limit'  => $validated['branch_limit'] ?? null,
                    'storage_limit' => $validated['storage_limit'] ?? null,
                    'is_active'     => $validated['is_active'],
                    'features'      => $featuresArray,
                ]);
            });

            return response()->json([
                'message' => 'Plan created successfully',
                'plan' => $plan
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $validated = $this->validatePlan($request);
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        try {
            DB::transaction(function () use ($plan, $validated, $featuresArray, $request) {
                $updateData = [
                    'name'          => $validated['name'],
                    'price'         => $validated['price'] ?? null,
                    'start_date'    => $validated['start_date'] ?? null,
                    'end_date'      => $validated['end_date'] ?? null,
                    'duration'      => $request->duration,
                    'total_amount'  => $request->total_amount,
                    'subtitle'      => $validated['subtitle'] ?? null,
                    'user_limit'    => $validated['user_limit'] ?? null,
                    'branch_limit'  => $validated['branch_limit'] ?? null,
                    'storage_limit' => $validated['storage_limit'] ?? null,
                    'is_active'     => $validated['is_active'],
                    'features'      => $featuresArray,
                ];
                $plan->update($updateData);
            });

            return response()->json([
                'message' => 'Plan updated successfully',
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiDestroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        try {
            DB::transaction(function () use ($plan) {
                $plan->delete();
            });

            return response()->json(['message' => 'Plan deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'price'          => ['nullable', 'numeric', 'min:0'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'subtitle'       => ['nullable', 'string'],
            'user_limit'     => ['nullable', 'integer', 'min:0'],
            'branch_limit'   => ['nullable', 'integer', 'min:0'],
            'storage_limit'  => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['required', 'boolean'],
            'features'       => ['nullable', 'string'],
        ]);
    }
}
