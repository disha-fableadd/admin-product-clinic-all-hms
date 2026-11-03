@extends('layout.app')

@section('title', 'Purchase Invoice - #' . ($invoice->bill_no ?? $invoice->invoice_number))

@section('content')
@php
    $displayVal = function($val) {
        $trimmed = trim((string)($val ?? ''));
        return ($trimmed === '' || $trimmed === '-' || $trimmed === '--') ? 'N/A' : $val;
    };

    $displayClinicName = \App\Models\Setting::where('key', 'clinic_name')->value('value') ?? 'Fablead-HMS';

    // Comprehensive logo resolution (same as sales invoice & clinic PDFs)
    $resolvedLogoPath = null;
    $candidateLogos = array_filter([
        \App\Models\Setting::where('key', 'clinic_logo')->value('value'),
        'uploads/clinic_logos/1792133005_fablogo.webp',
        'admin/assets/img/cliniclogo.png',
        'admin/assets/img/cliniclogohalf.png',
        'admin/assets/img/logo.png',
    ]);

    foreach ($candidateLogos as $candidate) {
        if (empty($candidate) || $candidate === 'default-logo.png') {
            continue;
        }
        if (file_exists($candidate) && is_file($candidate)) {
            $resolvedLogoPath = $candidate;
            break;
        }
        $cleanCandidate = ltrim(str_replace('\\', '/', $candidate), '/');
        if (file_exists(public_path($cleanCandidate)) && is_file(public_path($cleanCandidate))) {
            $resolvedLogoPath = public_path($cleanCandidate);
            break;
        }
        $baseCandidate = basename($candidate);
        if (file_exists(public_path('uploads/clinic_logos/' . $baseCandidate)) && is_file(public_path('uploads/clinic_logos/' . $baseCandidate))) {
            $resolvedLogoPath = public_path('uploads/clinic_logos/' . $baseCandidate);
            break;
        }
        if (file_exists(public_path('admin/assets/img/' . $baseCandidate)) && is_file(public_path('admin/assets/img/' . $baseCandidate))) {
            $resolvedLogoPath = public_path('admin/assets/img/' . $baseCandidate);
            break;
        }
    }

    $logoBase64 = null;
    if ($resolvedLogoPath && file_exists($resolvedLogoPath)) {
        $fileData = @file_get_contents($resolvedLogoPath);
        if ($fileData !== false) {
            $ext = pathinfo($resolvedLogoPath, PATHINFO_EXTENSION);
            $mime = ($ext === 'svg') ? 'image/svg+xml' : (($ext === 'webp') ? 'image/webp' : (($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png'));
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($fileData);
        }
    }

    $displayClinicPhone = \App\Models\Setting::where('key', 'clinic_phone')->value('value');
    $displayClinicEmail = \App\Models\Setting::where('key', 'clinic_email')->value('value');
    $displayClinicAddress = \App\Models\Setting::where('key', 'clinic_address')->value('value');
    $displayClinicCity = \App\Models\Setting::where('key', 'clinic_city')->value('value');
    $displayClinicState = \App\Models\Setting::where('key', 'clinic_state')->value('value');
    $addressParts = array_filter([$displayClinicAddress ?? '', $displayClinicCity ?? '', $displayClinicState ?? '']);

    $vendor = $invoice->vendor;
    $vendorName = $vendor ? $vendor->name : ($invoice->vendor_name ?? 'Direct Supplier');
    $vendorPhone = $vendor ? $vendor->phone : ($invoice->vendor_phone ?? '-');
    $vendorAddress = $vendor ? ($vendor->address ?? '-') : '-';
    $vendorGst = $vendor ? ($vendor->gst_number ?? '-') : '-';

    $billNo = $invoice->bill_no ?? $invoice->invoice_number ?? ('BILL-' . $invoice->id);
    $billDate = $invoice->purchase_date ? date('d-M-Y', strtotime($invoice->purchase_date)) : date('d-M-Y', strtotime($invoice->created_at));
    $dueDate = $invoice->due_date ? date('d-M-Y', strtotime($invoice->due_date)) : 'Immediate / N/A';

    $products = is_string($invoice->products) ? json_decode($invoice->products, true) : ($invoice->products ?? []);
    if (empty($products) && method_exists($invoice, 'purchases')) {
        $products = $invoice->purchases;
    }

    $grandTotal = (float)($invoice->grand_total ?? ($invoice->final_payable ?? 0));
    $remaining = (float)($invoice->remaining_amount ?? 0);
    $paidAmount = (float)($invoice->paid_amount ?? max(0, $grandTotal - $remaining));
    $taxableAmount = (float)($invoice->taxable_amount ?? ($invoice->subtotal ?? ($grandTotal - ($invoice->cgst ?? 0) - ($invoice->sgst ?? 0))));
    $itemDiscount = (float)($invoice->item_discount ?? 0);
    $billDiscount = (float)($invoice->bill_discount ?? 0);
    $cgst = (float)($invoice->cgst ?? 0);
    $sgst = (float)($invoice->sgst ?? 0);
@endphp

<style>
    /* Screen View Container */
    .invoice-screen-container {
        padding: 20px 0 40px 0;
        background: #f4f7f6;
        min-height: 100vh;
    }

    .pill-btn {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 8px 22px;
        font-size: 13.5px;
    }
    .btn-clinic-primary {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #fff !important;
    }
    .btn-clinic-primary:hover {
        background-color: #f67e65 !important;
        color: #fff !important;
    }

    /* Standard Clinic A4 Paper Wrapper (Matches Sales & Treatment PDFs) */
    .invoice-wrapper {
        width: 190mm;
        max-width: 190mm;
        margin: 0 auto;
        padding: 14px 16px;
        background-color: #fff;
        border: none;
        border-radius: 8px;
        box-sizing: border-box;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11.5px;
        color: #222;
        line-height: 1.35;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .clinic-title {
        font-size: 16px;
        font-weight: bold;
        color: #1a4d36;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .clinic-sub {
        font-size: 10.5px;
        color: #444;
        line-height: 1.3;
    }

    .header-divider {
        border: none;
        border-top: 1.5px solid #1e293b;
        margin: 8px 0 10px 0;
    }

    .details-container {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #d0e4dc;
        background-color: #f7faf8;
    }

    .details-box {
        padding: 6px 10px;
        vertical-align: top;
    }

    .details-box-left {
        width: 50%;
        border-right: 1px solid #d0e4dc;
    }

    .details-box-right {
        width: 50%;
    }

    .section-heading {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        color: #0c4b33;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        padding-bottom: 2px;
        border-bottom: 1px dashed #c4ddd1;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11px;
    }

    .info-table td,
    .info-table th {
        padding: 2px 0;
        vertical-align: top;
        font-size: 11px;
    }

    .info-label {
        width: 110px;
        color: #4b5563;
        font-weight: normal;
    }

    .info-sep {
        width: 10px;
        color: #4b5563;
        text-align: center;
    }

    .info-val {
        color: #1f2937;
    }

    .section-banner {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #1f2937;
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        padding: 4px 0;
        letter-spacing: 0.6px;
        margin-top: 8px;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .invoice-box {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11px;
    }

    .invoice-box th {
        background-color: #cfece0;
        color: #0c4b33;
        font-weight: bold;
        text-align: center !important;
        padding: 5px 6px;
        border: 1px solid #b8dfd0;
        font-size: 10.5px;
        letter-spacing: 0.3px;
        vertical-align: middle !important;
    }

    .invoice-box td {
        padding: 5px 6px;
        border: 1px solid #d5e5dd;
        color: #333;
        font-size: 10.5px;
        vertical-align: middle !important;
    }

    .invoice-box tbody tr:nth-child(even) {
        background-color: #fbfdfc;
    }

    .totals-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        border: 1px solid #d0e4dc;
    }

    .totals-table td {
        padding: 3px 6px;
    }

    .declaration-signatory {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        border: 1px solid #d0e4dc;
        background-color: #f7faf8;
        table-layout: fixed;
    }

    .declaration-signatory td {
        padding: 6px 8px;
        vertical-align: top;
    }

    .declaration-cell {
        width: 55%;
        border-right: 1px solid #d0e4dc;
    }

    .signatory-cell {
        width: 45%;
        position: relative;
        height: 55px;
        vertical-align: top !important;
    }

    .signatory-company {
        text-align: right;
        font-size: 10px;
        color: #0c4b33;
        font-weight: bold;
    }

    .signatory-bottom {
        position: absolute;
        bottom: 4px;
        right: 8px;
        text-align: right;
        font-size: 9.5px;
        color: #0c4b33;
    }

    .footer-note {
        margin-top: 6px;
        padding-top: 4px;
        border-top: 1px dashed #c0d8cd;
        text-align: center;
        font-size: 9.5px;
        color: #666;
        line-height: 1.35;
    }

    /* Print Styles */
    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm 6mm;
        }
        body, html {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .main-wrapper, .page-wrapper, .content, .invoice-screen-container {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .header, .sidebar, .sidebar-overlay, .footer-copyright, .mobile-bottom-nav, .no-print {
            display: none !important;
        }
        .invoice-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10px 12px !important;
            border: 1.5px solid #222 !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="page-wrapper">
    <div class="invoice-screen-container">
        <div class="container-fluid">
            <!-- Top Action Controls (hidden on print) -->
            <div class="no-print d-flex justify-content-between align-items-center mb-3 px-3 mx-auto flex-wrap gap-2" style="max-width: 195mm;">
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #1e293b;">
                        Purchase Bill <span style="color: #f89884;">#{{ $billNo }}</span>
                    </h5>
                    <small class="text-muted">Standard Project Print / PDF Format</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchase.lists') }}" class="btn btn-outline-secondary pill-btn" style="margin-right:10px">
                        <i class="fa fa-arrow-left me-1"></i> Back to Bills
                    </a>
                    <button onclick="downloadPDF()" class="btn btn-clinic-primary pill-btn" id="downloadPdfBtn">
                        <i class="fa fa-download me-1"></i> Download PDF
                    </button>
                  
                </div>
            </div>

            <!-- Authentic Clinic A4 Receipt Container -->
            <div class="invoice-wrapper" id="invoiceWrapper">
                <!-- Clinic Header Table -->
                <table class="header-table">
                    <tr>
                        <td style="width: 40%; vertical-align: middle;">
                            @if(!empty($logoBase64))
                                <img src="{{ $logoBase64 }}" alt="Clinic Logo" style="max-height: 60px; max-width: 180px;">
                            @elseif(!empty($resolvedLogoPath) && file_exists($resolvedLogoPath))
                                <img src="{{ asset($resolvedLogoPath) }}" alt="Clinic Logo" style="max-height: 60px; max-width: 180px;">
                            @else
                                <div class="clinic-title">{{ $displayClinicName }}</div>
                            @endif
                        </td>
                        <td style="width: 60%; text-align: right; vertical-align: middle;">
                            <div class="clinic-title">{{ $displayClinicName }}</div>
                            @if(!empty($addressParts))
                                <div class="clinic-sub">
                                    {{ implode(', ', $addressParts) }}
                                </div>
                            @endif
                            <div class="clinic-sub">
                                @if(!empty($displayClinicPhone))PHONE: {{ $displayClinicPhone }}@endif
                                @if(!empty($displayClinicPhone) && !empty($displayClinicEmail)) | @endif
                                @if(!empty($displayClinicEmail))EMAIL: {{ $displayClinicEmail }}@endif
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Divider Line -->
                <hr class="header-divider">

                <!-- Vendor & Purchase Overview Box -->
                <table class="details-container">
                    <tr>
                        <!-- Vendor Details -->
                        <td class="details-box details-box-left">
                            <div class="section-heading">VENDOR DETAILS:</div>
                            <table class="info-table">
                                <tr>
                                    <td class="info-label">Vendor Name</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val"><strong>{{ $vendorName }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Contact / Phone</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">{{ $vendorPhone }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Address</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">{{ $vendorAddress }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">GSTIN</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">{{ $vendorGst }}</td>
                                </tr>
                            </table>
                        </td>

                        <!-- Purchase Overview -->
                        <td class="details-box details-box-right">
                            <div class="section-heading">INVOICE OVERVIEW:</div>
                            <table class="info-table">
                                <tr>
                                    <td class="info-label">Bill No</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val"><strong>#{{ $billNo }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="info-label">System Ref</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">#{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Purchase Date</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">{{ $billDate }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Payment Due Date</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">{{ $dueDate }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Payment Mode</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val"><strong>{{ ucfirst($invoice->payment_mode ?? 'Cash') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Payment Status</td>
                                    <td class="info-sep">:</td>
                                    <td class="info-val">
                                        <strong style="color: {{ $remaining <= 0 ? '#10b981' : '#ef4444' }};">
                                            {{ strtoupper($invoice->payment_status ?? ($remaining <= 0 ? 'COMPLETED' : 'PENDING')) }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Items Banner -->
                <div class="section-banner">
                    MEDICINE & PRODUCT PARTICULARS
                </div>

                <!-- Product Items Table -->
                <table class="invoice-box">
                    <thead>
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 30%; text-align: left !important;">Medicine / Item</th>
                            <th style="width: 14%;">Batch</th>
                            <th style="width: 11%;">Expiry</th>
                            <th style="width: 7%;">Qty</th>
                            <th style="width: 6%;">Free</th>
                            <th style="width: 9%;">Price (₹)</th>
                            <th style="width: 9%;">MRP (₹)</th>
                            <th style="width: 8%;">GST %</th>
                            <th style="width: 12%; text-align: right !important;">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sr = 1; @endphp
                        @forelse($products as $prod)
                            @php
                                $p = is_array($prod) ? (object)$prod : $prod;
                                $itemName = $p->item ?? $p->product_name ?? ($p->medicine->name ?? 'Medicine Item');
                                $batch = $p->batch_no ?? $p->batch ?? '-';
                                $expiry = $p->expiry ?? $p->expiry_date ?? '-';
                                $qty = $p->quantity ?? $p->qty ?? 1;
                                $freeQty = $p->free_quantity ?? $p->free_qty ?? 0;
                                $price = (float)($p->cost_price ?? $p->purchase_price ?? $p->price ?? 0);
                                $mrp = (float)($p->mrp ?? 0);
                                $taxRate = (float)($p->tax_rate ?? $p->gst ?? $p->gst_rate ?? 0);
                                $lineTotal = (float)($p->subtotal ?? $p->total_amount ?? $p->total ?? ($qty * $price));
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $sr++ }}</td>
                                <td style="text-align: left;">
                                    <strong>{{ $itemName }}</strong>
                                </td>
                                <td style="text-align: center;">{{ $batch }}</td>
                                <td style="text-align: center;">{{ $expiry }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $qty }}</td>
                                <td style="text-align: center;">{{ $freeQty }}</td>
                                <td style="text-align: center;">{{ number_format($price, 2) }}</td>
                                <td style="text-align: center;">{{ number_format($mrp, 2) }}</td>
                                <td style="text-align: center;">{{ $taxRate }}%</td>
                                <td style="text-align: right; font-weight: bold;">{{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-2 text-muted">No items found on this bill</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Financial Totals Summary Table -->
                <table class="totals-table">
                    <tr>
                        <td style="width: 55%; vertical-align: top; background-color: #fafbfc; border-right: none;">
                            <div style="font-size: 11px; color: #475569; line-height: 1.6;">
                                <div style="font-weight: bold; color: #0c4b33; margin-bottom: 4px; text-transform: uppercase;">
                                    Billing & Credit Terms:
                                </div>
                                <strong>Payment Due Days:</strong> {{ $invoice->payment_due_days ?? 0 }} days<br>
                                <strong>Interest Rate:</strong> {{ $invoice->interest_before_due ?? 0 }}% (Before Due) / {{ $invoice->interest_after_due ?? 0 }}% (After Due)<br>
                                @if(($invoice->interest_amount ?? 0) > 0)
                                    <strong>Interest Amount:</strong> ₹{{ number_format($invoice->interest_amount, 2) }}<br>
                                @endif
                                @if($invoice->remark || $invoice->remarks)
                                    <strong>Remarks:</strong> {{ $invoice->remark ?? $invoice->remarks }}<br>
                                @endif
                            </div>
                        </td>
                        <td style="width: 45%; vertical-align: top; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #4b5563;">Taxable Subtotal:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right; font-weight: bold;">₹{{ number_format($taxableAmount, 2) }}</td>
                                </tr>
                                @if($itemDiscount > 0)
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #166534;">Item Discount:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right; color: #166534; font-weight: bold;">-₹{{ number_format($itemDiscount, 2) }}</td>
                                </tr>
                                @endif
                                @if($billDiscount > 0)
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #166534;">Bill Discount:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right; color: #166534; font-weight: bold;">-₹{{ number_format($billDiscount, 2) }}</td>
                                </tr>
                                @endif
                                @if($cgst > 0)
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #4b5563;">CGST:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right;">₹{{ number_format($cgst, 2) }}</td>
                                </tr>
                                @endif
                                @if($sgst > 0)
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #4b5563;">SGST:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right;">₹{{ number_format($sgst, 2) }}</td>
                                </tr>
                                @endif
                                @if(($invoice->round_off ?? 0) != 0)
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #4b5563;">Round Off:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right;">₹{{ number_format($invoice->round_off, 2) }}</td>
                                </tr>
                                @endif
                                <tr style="background-color: #f7faf8;">
                                    <td style="padding: 6px 8px; border-bottom: 1px solid #b8dfd0; font-size: 12.5px; font-weight: bold; color: #0c4b33;">Grand Total:</td>
                                    <td style="padding: 6px 8px; border-bottom: 1px solid #b8dfd0; text-align: right; font-size: 12.5px; font-weight: bold; color: #0c4b33;">₹{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; color: #15803d; font-weight: bold;">Total Paid:</td>
                                    <td style="padding: 4px 8px; border-bottom: 1px solid #d5e5dd; text-align: right; color: #15803d; font-weight: bold;">₹{{ number_format($paidAmount, 2) }}</td>
                                </tr>
                                @if($remaining > 0)
                                <tr style="background-color: #fef2f2;">
                                    <td style="padding: 5px 8px; color: #b91c1c; font-weight: bold;">Remaining Balance:</td>
                                    <td style="padding: 5px 8px; text-align: right; color: #b91c1c; font-weight: bold;">₹{{ number_format($remaining, 2) }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Payment History Section Table (if any payments recorded) -->
                @if($invoice->payments && $invoice->payments->where('isDeleted', 0)->isNotEmpty())
                    <div class="section-banner" style="margin-top: 14px;">
                        PAYMENT TRANSACTIONS
                    </div>
                    <table class="invoice-box">
                        <thead>
                            <tr>
                                <th style="width: 22%;">Date</th>
                                <th style="width: 18%;">Amount (₹)</th>
                                <th style="width: 18%;">Mode</th>
                                <th style="width: 22%;">Remarks</th>
                                <th style="width: 20%;">Remaining Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments->where('isDeleted', 0) as $pmt)
                                <tr>
                                    <td style="text-align: center;">{{ date('d-M-Y', strtotime($pmt->payment_date ?? $pmt->created_at)) }}</td>
                                    <td style="text-align: center; font-weight: bold;">₹{{ number_format($pmt->payment_amount, 2) }}</td>
                                    <td style="text-align: center;">{{ ucfirst($pmt->payment_method ?? 'Cash') }}</td>
                                    <td style="text-align: center;">{{ $pmt->remarks ?: '-' }}</td>
                                    <td style="text-align: center;">₹{{ number_format((float)($pmt->remaining_amount ?? 0), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <!-- Declaration & Authorized Signatory -->
                <table class="declaration-signatory">
                    <tr>
                        <td class="declaration-cell">
                            <div style="font-size: 10.5px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                                Terms & Conditions:
                            </div>
                            <div style="font-size: 10px; color: #555; line-height: 1.45;">
                                1. All medicines and pharmacy goods received and verified as per purchase bill.<br>
                                2. Credit terms, discounts and payment terms apply as per vendor agreement.<br>
                                3. This is an official computer-generated purchase invoice for pharmacy inventory records.
                            </div>
                        </td>
                        <td class="signatory-cell">
                            <div class="signatory-company">For {{ $displayClinicName }}</div>
                            <div class="signatory-bottom">Authorized Signatory</div>
                        </td>
                    </tr>
                </table>

                <!-- Footer Note -->
                <div class="footer-note">
                    Hospital Management System • Verified Pharmacy Purchase Document<br>
                    <span>This is a computer-generated invoice and requires no physical signature.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        const btn = document.getElementById('downloadPdfBtn');
        const origHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Downloading...';
            btn.disabled = true;
        }

        const element = document.getElementById('invoiceWrapper');
        const prevShadow = element.style.boxShadow;
        const prevBorder = element.style.border;
        const prevRadius = element.style.borderRadius;
        element.style.boxShadow = 'none';
        element.style.borderRadius = '0';
        element.style.border = '2px solid #1e293b';

        const opt = {
            margin:       [6, 8, 6, 8],
            filename:     'Purchase_Invoice_{{ $billNo }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                logging: false,
                scrollY: 0,
                scrollX: 0
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opt).from(element).save().then(function() {
            element.style.boxShadow = prevShadow;
            element.style.border = prevBorder;
            element.style.borderRadius = prevRadius;
            if (btn) {
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }
        }).catch(function(err) {
            console.error(err);
            element.style.boxShadow = prevShadow;
            element.style.border = prevBorder;
            element.style.borderRadius = prevRadius;
            if (btn) {
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }
            window.print();
        });
    }

    @if(request('download_pdf') == 1)
    window.addEventListener('load', function() {
        setTimeout(function() {
            downloadPDF();
        }, 600);
    });
    @elseif(request('print') == 1)
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
    @endif
</script>
@endsection
