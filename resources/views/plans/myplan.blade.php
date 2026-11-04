@extends('layout.app')

@section('title', 'My Plan Details')

@section('content')
<style>
    /* Modal close button styling */
    .modal .btn-close-custom {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        border-radius: 50% !important;
        border: none !important;
        outline: none !important;
        font-size: 15px !important;
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
        padding: 0 !important;
        line-height: 1 !important;
        text-decoration: none !important;
    }

    .modal .btn-close-custom.btn-close-white-style {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }
    .modal .btn-close-custom.btn-close-white-style:hover {
        background-color: rgba(255, 255, 255, 0.35) !important;
        color: #ffffff !important;
        transform: scale(1.08);
    }

    .modal .btn-close-custom.btn-close-dark-style {
        background-color: #f1f3f5 !important;
        color: #495057 !important;
    }
    .modal .btn-close-custom.btn-close-dark-style:hover {
        background-color: #e9ecef !important;
        color: #212529 !important;
        transform: scale(1.08);
    }

    /* Clinic Plan Card styling */
    .plan-card-banner {
        background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 52%, #38b2ac 100%);
        color: #ffffff;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(27, 67, 50, 0.18);
        position: relative;
        overflow: hidden;
    }

    .plan-card-banner::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .plan-amount-glass {
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 20px 32px;
        text-align: center;
        min-width: 220px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .feature-item-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #334155;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease-in-out;
    }

    .feature-item-card:hover {
        border-color: #2e7d6b;
        background-color: #f0fdf4;
        box-shadow: 0 3px 10px rgba(46, 125, 107, 0.08);
        transform: translateY(-1px);
    }

    .feature-check-icon {
        background-color: #e6f9ed;
        color: #2e7d6b;
        width: 24px;
        height: 24px;
        min-width: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }

    .btn-clinic-primary {
        background-color: #2e7d6b !important;
        border-color: #2e7d6b !important;
        color: #ffffff !important;
    }
    .btn-clinic-primary:hover {
        background-color: #1f4e43 !important;
        border-color: #1f4e43 !important;
        color: #ffffff !important;
    }

    .btn-clinic-outline {
        border: 1.5px solid #2e7d6b !important;
        color: #2e7d6b !important;
        background-color: #ffffff !important;
    }
    .btn-clinic-outline:hover {
        background-color: #e6f9ed !important;
        color: #1f4e43 !important;
    }

    .btn-clinic-dark-outline {
        border: 1.5px solid #1f4e43 !important;
        color: #1f4e43 !important;
        background-color: #ffffff !important;
    }
    .btn-clinic-dark-outline:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .btn-clinic-back {
        background-color: #9fd9c1 !important;
        color: #1f4e43 !important;
        border: none !important;
    }
    .btn-clinic-back:hover {
        background-color: #8acbb0 !important;
        color: #0d2822 !important;
    }
</style>
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="page-title">
                <h4 style="font-weight: 700; color: #1e293b; margin-bottom: 5px;">My Plan Details</h4>
                <h6 style="color: #64748b; font-weight: 400; font-size: 14px;">Overview of your active subscription plan and limits</h6>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-clinic-primary fw-semibold" style="border-radius: 7px; padding: 8px 18px;" data-toggle="modal" data-target="#invoicesListModal" data-bs-toggle="modal" data-bs-target="#invoicesListModal">
                    <i class="fas fa-file-pdf me-1 mr-1"></i> Download Invoice
                </button>
                <button type="button" class="btn btn-clinic-outline fw-semibold" style="border-radius: 7px; padding: 8px 18px;" data-toggle="modal" data-target="#renewPlanModal" data-bs-toggle="modal" data-bs-target="#renewPlanModal">
                    <i class="fas fa-sync-alt me-1 mr-1"></i> Renew Plan
                </button>
                <button type="button" class="btn btn-clinic-dark-outline fw-semibold" style="border-radius: 7px; padding: 8px 18px;" data-toggle="modal" data-target="#planHistoryModal" data-bs-toggle="modal" data-bs-target="#planHistoryModal">
                    <i class="fas fa-history me-1 mr-1"></i> Plan History
                </button>
                <a href="javascript:history.back()" class="btn btn-clinic-back fw-semibold" style="border-radius: 7px; padding: 8px 18px;">
                    <i class="fas fa-arrow-left me-1 mr-1"></i> Back
                </a>
            </div>
        </div>

        @php
            $planDaysRemaining = 0;
            $isFutureOrToday = false;
            if ($plan->end_date) {
                $endDate = \Carbon\Carbon::parse($plan->end_date)->startOfDay();
                $now = \Carbon\Carbon::now()->startOfDay();
                $planDaysRemaining = $now->diffInDays($endDate, false); 
                $isFutureOrToday = $endDate->greaterThanOrEqualTo($now);
            }
            $renewalPrice = $plan->renewal_price ?? ($plan->total_amount ?? ($plan->price ?? 0));
            $currentCycle = strtolower(trim((string)($plan->duration ?? 'year')));
            
            $renewalStartDateCarbon = ($plan->end_date && $isFutureOrToday)
                ? \Carbon\Carbon::parse($plan->end_date)
                : \Carbon\Carbon::today();

            $renewalEndDateCarbon = (str_contains($currentCycle, 'month'))
                ? $renewalStartDateCarbon->copy()->addMonth()
                : $renewalStartDateCarbon->copy()->addYear();

            $extendedRenewalStartDate = $renewalStartDateCarbon->format('d-m-Y');
            $extendedRenewalDate = $renewalEndDateCarbon->format('d-m-Y');

            $tenantInfo = function_exists('tenant') ? tenant() : null;
            $purchasedBy = $tenantInfo['brand_name'] ?? ($tenantInfo['client_name'] ?? ($user->clinic_name ?? ($user->fullname ?? 'Fablead HMS Client')));
        @endphp

        <!-- Active Plan Banner Card -->
        <div class="card plan-card-banner mb-4 border-0">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-4 position-relative" style="z-index: 1;">
                <div class="mb-2 mb-md-0" style="flex: 1; min-width: 280px;">
                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                        <h2 class="mb-0 text-white text-capitalize" style="font-weight: 700; font-size: 32px; letter-spacing: -0.5px;">{{ $plan->name }}</h2>
                        @if($planDaysRemaining <= 30)
                        <span class="badge rounded-pill" style="background-color: rgba(239, 68, 68, 0.25); color: #fecaca; border: 1px solid #f87171; padding: 6px 12px; font-weight: 600; font-size: 12px;">
                            <i class="fas fa-exclamation-triangle me-1 mr-1"></i> {{ $planDaysRemaining > 0 ? 'Expiring in ' . $planDaysRemaining . ' days' : 'Expired' }}
                        </span>
                        @endif
                        <span class="badge rounded-pill" style="background-color: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px 14px; font-weight: 600; font-size: 13px;">
                            Renewal Price: ₹{{ number_format((float)$renewalPrice, 2) }}
                        </span>
                    </div>
                    <p class="mb-4 text-white-50" style="font-size: 14.5px; font-weight: 500;">
                        Purchased By <span class="text-white fw-bold">{{ $purchasedBy }}</span>
                    </p>
                    <div class="d-flex gap-4 flex-wrap text-white" style="font-size: 13.5px; font-weight: 500;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="far fa-calendar-alt text-warning me-1 mr-1"></i> Start Date: <strong>{{ $plan->start_date ? \Carbon\Carbon::parse($plan->start_date)->format('d-m-Y') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="far fa-calendar-check text-warning me-1 mr-1"></i> End Date: <strong>{{ $plan->end_date ? \Carbon\Carbon::parse($plan->end_date)->format('d-m-Y') : 'N/A' }}</strong>
                        </div>
                        @if($plan->user_limit)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-users text-warning me-1 mr-1"></i> User Limit: <strong>{{ $plan->user_limit }}</strong>
                        </div>
                        @endif
                        @if($plan->branch_limit)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-building text-warning me-1 mr-1"></i> Branch Limit: <strong>{{ $plan->branch_limit }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Right Amount Box -->
                <div class="plan-amount-glass">
                    <div style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 1.2px; color: #ffffff; opacity: 0.9; margin-bottom: 6px; font-weight: 700;">TOTAL AMOUNT</div>
                    <h2 class="mb-1 text-white" style="font-weight: 800; font-size: 34px;">₹{{ number_format((float)($plan->total_amount ?? ($plan->price ?? 0)), 2) }}</h2>
                    <div style="font-size: 13.5px; color: #bbf7d0; font-weight: 600;">
                        @if($plan->duration == 'month')
                            1 Month
                        @elseif($plan->duration == 'year')
                            1 Year
                        @else
                            {{ ucfirst($plan->duration ?? '1 Year') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Included Features Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important; background: #ffffff;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 style="font-weight: 700; color: #1e293b; font-size: 16.5px;" class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2 mr-2" style="font-size: 20px;"></i> Included Features
                </h5>
            </div>
            <div class="card-body p-4 pt-3">
                <div class="row">
                @php
                    $featuresList = [];
                    if (is_array($plan->features) && count($plan->features) > 0) {
                        $featuresList = $plan->features;
                    } elseif (!empty($tenantInfo['modules']) && is_array($tenantInfo['modules'])) {
                        $featuresList = $tenantInfo['modules'];
                    } else {
                        $featuresList = ['Dashboard', 'Patients', 'Appointments', 'Medicines', 'Staffs', 'Followup', 'Clinical Services', 'Billing & Invoices'];
                    }
                @endphp

                @foreach($featuresList as $feature)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="feature-item-card">
                            <span class="feature-check-icon">
                                <i class="fas fa-check"></i>
                            </span>
                            <span style="line-height: 1.3;">{{ $feature }}</span>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <!-- Renew Plan Modal -->
        <div class="modal fade" id="renewPlanModal" tabindex="-1" aria-labelledby="renewPlanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #1b4332, #2d6a4f); border-bottom: none; padding: 20px 24px;">
                        <h5 class="modal-title fw-bold" id="renewPlanModalLabel">
                            <i class="fas fa-sync-alt me-2 mr-2 text-warning"></i> Renew Current Subscription
                        </h5>
                        <button type="button" class="btn-close-custom btn-close-white-style" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 58px; height: 58px; background-color: #e6f9ed; color: #2e7d6b;">
                                <i class="fas fa-file-invoice-dollar fs-3" style="font-size: 26px;"></i>
                            </div>
                            <h4 class="fw-bold mb-1" style="color: #1b4332;">{{ $plan->name }}</h4>
                            <span class="badge bg-light text-secondary border px-3 py-1">Active Plan</span>
                        </div>

                        <div class="bg-light rounded p-3 mb-3 border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Billing Cycle:</span>
                                <span class="fw-semibold text-capitalize">{{ $plan->duration ?? '1 Year' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Current Expiry:</span>
                                <span class="fw-semibold">{{ $plan->end_date ? \Carbon\Carbon::parse($plan->end_date)->format('d-m-Y') : 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Renewal Start Date:</span>
                                <span class="fw-semibold text-dark">{{ $extendedRenewalStartDate }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Extended Expiry Date:</span>
                                <span class="fw-bold text-success">{{ $extendedRenewalDate }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold" style="font-size: 16px;">Renewal Payable:</span>
                                <span class="fw-bold" style="font-size: 22px; color: #2e7d6b;">₹{{ number_format((float)$renewalPrice, 2) }}</span>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-0" style="background-color: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
                            <i class="fas fa-shield-alt me-2 mr-2 fs-5" style="font-size: 20px;"></i>
                            <span>Secure checkout powered by Razorpay. Plan updates immediately upon verification.</span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn text-white fw-bold px-4" id="btnConfirmRenewal" style="background-color: #2e7d6b;" onclick="processRenewalPayment()">
                            <span class="spinner-border spinner-border-sm me-1 mr-1 d-none" id="renewalSpinner"></span>
                            <i class="fas fa-lock me-1 mr-1"></i> Pay ₹{{ number_format((float)$renewalPrice, 2) }} & Renew
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Invoices List Modal -->
        <div class="modal fade" id="invoicesListModal" tabindex="-1" aria-labelledby="invoicesListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #1b4332, #2d6a4f); border-bottom: none; padding: 20px 24px;">
                        <h5 class="modal-title fw-bold" id="invoicesListModalLabel">
                            <i class="fas fa-file-invoice me-2 mr-2 text-warning"></i> Download Tax Invoice / Receipt
                        </h5>
                        <button type="button" class="btn-close-custom btn-close-white-style" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th class="text-center" style="width: 130px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($paymentHistory) && count($paymentHistory) > 0)
                                        @foreach($paymentHistory as $tx)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark">INV-{{ !empty($tx['created_at']) ? \Carbon\Carbon::parse($tx['created_at'])->format('Y') : date('Y') }}-{{ str_pad((string)($tx['id'] ?? 1), 4, '0', STR_PAD_LEFT) }}</span>
                                                </td>
                                                <td>{{ !empty($tx['created_at']) ? \Carbon\Carbon::parse($tx['created_at'])->format('d M Y') : 'N/A' }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $plan->name }}</div>
                                                    <small class="text-muted">{{ $tx['description'] ?? 'Subscription Payment' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge {{ ($tx['payment_type'] ?? '') === 'renewal' ? 'bg-info text-white' : 'bg-success text-white' }}" style="text-transform: uppercase;">
                                                        {{ $tx['payment_type'] ?? 'Purchase' }}
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-dark">₹{{ number_format((float)($tx['amount'] ?? 0), 2) }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('plans.invoice.pdf', ['transaction_id' => $tx['id'] ?? null]) }}" target="_blank" class="btn btn-sm btn-clinic-primary fw-semibold" style="border-radius: 6px; padding: 6px 14px;">
                                                        <i class="fas fa-download me-1 mr-1"></i> PDF
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">INV-{{ date('Y') }}-{{ str_pad((string)$plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>{{ $plan->start_date ? \Carbon\Carbon::parse($plan->start_date)->format('d M Y') : date('d M Y') }}</td>
                                            <td><div class="fw-semibold text-dark">{{ $plan->name ?? 'Active Plan' }}</div></td>
                                            <td><span class="badge bg-success text-white">PURCHASE</span></td>
                                            <td class="fw-bold text-dark">₹{{ number_format((float)($plan->total_amount ?? $plan->price ?? 0), 2) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('plans.invoice.pdf') }}" target="_blank" class="btn btn-sm btn-clinic-primary fw-semibold" style="border-radius: 6px; padding: 6px 14px;">
                                                    <i class="fas fa-download me-1 mr-1"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 px-4 py-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan & Payment History Modal (matching Image 2) -->
        <div class="modal fade" id="planHistoryModal" tabindex="-1" aria-labelledby="planHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                    <div class="modal-header border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="planHistoryModalLabel" style="font-weight: 700; color: #1e293b;">
                            <i class="fas fa-history text-secondary me-2 mr-2"></i> Plan & Payment History
                        </h5>
                        <button type="button" class="btn-close-custom btn-close-dark-style" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Plan / Description</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 120px;">Invoice / Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($paymentHistory) && count($paymentHistory) > 0)
                                        @foreach($paymentHistory as $tx)
                                            <tr>
                                                <td>
                                                    <strong>{{ !empty($tx['created_at']) ? \Carbon\Carbon::parse($tx['created_at'])->format('d M Y, h:i A') : 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $plan->name ?? 'Subscription Plan' }}</div>
                                                    <small class="text-muted">{{ $tx['description'] ?? ($tx['razorpay_order_id'] ?? 'Online Payment') }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $isRenewal = strtolower((string)($tx['payment_type'] ?? '')) === 'renewal';
                                                    @endphp
                                                    <span class="badge {{ $isRenewal ? 'bg-info text-white' : 'bg-primary text-white' }}" style="text-transform: uppercase; font-size: 11px; padding: 5px 8px;">
                                                        {{ $isRenewal ? 'RENEWAL' : 'PURCHASE' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-credit-card me-1 mr-1 text-muted"></i> {{ $tx['payment_method'] ?? 'Razorpay' }}
                                                </td>
                                                <td class="fw-bold text-dark">
                                                    ₹{{ number_format((float) ($tx['amount'] ?? 0), 2) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $st = strtolower(trim((string) ($tx['status'] ?? 'paid')));
                                                    @endphp
                                                    @if(in_array($st, ['paid', 'completed', 'success']))
                                                        <span class="badge bg-success text-white" style="font-size: 11px; padding: 5px 8px;"><i class="fas fa-check-circle me-1 mr-1"></i> Paid</span>
                                                    @elseif($st === 'pending')
                                                        <span class="badge bg-warning text-dark" style="font-size: 11px; padding: 5px 8px;"><i class="fas fa-clock me-1 mr-1"></i> Pending</span>
                                                    @else
                                                        <span class="badge bg-danger text-white" style="font-size: 11px; padding: 5px 8px;"><i class="fas fa-times-circle me-1 mr-1"></i> {{ ucfirst($st) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('plans.invoice.pdf', ['transaction_id' => $tx['id'] ?? null]) }}" target="_blank" class="btn btn-sm text-white" style="background-color: #2e7d6b; border-radius: 4px; padding: 5px 12px;" title="Download PDF Invoice / Receipt">
                                                        <i class="fas fa-file-pdf me-1 mr-1"></i> PDF
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(!empty($planHistory) && count($planHistory) > 0)
                                        @foreach($planHistory as $historyItem)
                                            <tr>
                                                <td>{{ $historyItem->created_at ? \Carbon\Carbon::parse($historyItem->created_at)->format('d M Y, h:i A') : date('d M Y') }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $historyItem->name }}</div>
                                                    <small class="text-muted">{{ $historyItem->start_date ? \Carbon\Carbon::parse($historyItem->start_date)->format('d-m-Y') : '-' }} to {{ $historyItem->end_date ? \Carbon\Carbon::parse($historyItem->end_date)->format('d-m-Y') : '-' }}</small>
                                                </td>
                                                <td><span class="badge bg-primary text-white" style="text-transform: uppercase;">PURCHASE</span></td>
                                                <td><i class="fas fa-credit-card me-1 mr-1 text-muted"></i> Online</td>
                                                <td class="fw-bold text-dark">₹{{ number_format((float)($historyItem->total_amount ?? $historyItem->price ?? 0), 2) }}</td>
                                                <td>
                                                    @if($historyItem->is_active)
                                                        <span class="badge bg-success text-white"><i class="fas fa-check-circle me-1 mr-1"></i> Paid</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('plans.invoice.pdf', $historyItem->id) }}" target="_blank" class="btn btn-sm text-white" style="background-color: #2e7d6b; border-radius: 4px; padding: 5px 12px;" title="Download PDF Invoice">
                                                        <i class="fas fa-file-pdf me-1 mr-1"></i> PDF
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No plan payment history found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 px-4 py-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const currentPlanId = {{ $plan->id ?? 'null' }};
    const currentRenewalPrice = {{ (float) $renewalPrice }};
    const currentBillingDuration = '{{ $plan->duration ?? "year" }}';

    function notifyAlert(icon, title, text, callback = null) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#2e7d6b',
                confirmButtonText: 'OK'
            }).then(() => {
                if (callback) callback();
            });
        } else {
            alert(text);
            if (callback) callback();
        }
    }

    // Process Plan Renewal with Razorpay
    function processRenewalPayment() {
        const btn = document.getElementById('btnConfirmRenewal');
        const spinner = document.getElementById('renewalSpinner');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');

        fetch("{{ route('plans.razorpay.createOrder') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount: currentRenewalPrice,
                payment_type: 'renewal',
                description: 'Plan Renewal: {{ addslashes($plan->name ?? "Subscription") }}'
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');

            if (!data.success || !data.order) {
                if (data.message && data.message.includes('credentials not configured')) {
                    notifyAlert('warning', 'Payment Gateway Notice', 'Razorpay credentials have not been configured in Super Admin settings yet.');
                } else {
                    notifyAlert('error', 'Initialization Failed', data.message || 'Failed to initialize payment gateway.');
                }
                return;
            }

            const order = data.order;
            const options = {
                key: order.key || order.key_id,
                amount: order.amount,
                currency: order.currency || 'INR',
                name: order.name || 'HMS Plan Renewal',
                description: order.description || 'Renewing active subscription',
                order_id: order.id || order.order_id,
                prefill: order.prefill || {},
                theme: { color: '#2e7d6b' },
                modal: {
                    ondismiss: function() {
                        console.log('Payment modal dismissed by user.');
                    }
                },
                handler: function(response) {
                    verifyPayment({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                        payment_type: 'renewal',
                        amount: currentRenewalPrice,
                        billing_cycle: currentBillingDuration
                    });
                }
            };

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function(response) {
                notifyAlert('error', 'Payment Failed', response.error.description || 'Transaction declined.');
            });
            rzp.open();
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            notifyAlert('error', 'Payment Error', 'Error initializing payment: ' + err.message);
        });
    }

    function verifyPayment(paymentPayload) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Verifying Payment...',
                text: 'Please wait while your subscription plan is being verified and updated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        fetch("{{ route('plans.razorpay.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(paymentPayload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                notifyAlert('success', 'Plan Renewed Successfully!', data.message || 'Your subscription has been renewed successfully.', function() {
                    window.location.reload();
                });
            } else {
                notifyAlert('error', 'Verification Failed', data.message || 'Payment verification could not be completed.');
            }
        })
        .catch(err => {
            notifyAlert('error', 'Verification Error', 'Error communicating with server: ' + err.message);
        });
    }
</script>
@endpush
