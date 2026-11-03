<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Ledger Statement</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #222;
            background-color: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .invoice-wrapper {
            width: 190mm;
            min-height: 277mm;
            margin: 10mm auto 0;
            padding: 12px 14px;
            background-color: #fff;
            border: 1.5px solid #222;
            box-sizing: border-box;
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
            margin-bottom: 3px;
        }

        .clinic-sub {
            font-size: 11px;
            color: #444;
            line-height: 1.35;
        }

        .header-divider {
            border: none;
            border-top: 1.5px solid #222;
            margin: 10px 0 12px 0;
        }

        .details-container {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            background-color: #f7faf8;
            margin-bottom: 12px;
        }

        .details-box {
            padding: 8px 12px;
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
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #c4ddd1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-label {
            width: 110px;
            color: #4b5563;
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
            color: #0c4b33;
            font-size: 12.5px;
            font-weight: bold;
            text-align: center;
            padding: 6px 0;
            letter-spacing: 1px;
            margin-top: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            background-color: #cfece0;
            border: 1px solid #b8dfd0;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .invoice-box th {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: bold;
            text-align: center;
            padding: 6px 8px;
            border: 1px solid #b8dfd0;
            font-size: 11px;
            letter-spacing: 0.3px;
            vertical-align: middle;
        }

        .invoice-box td {
            padding: 5px 7px;
            border: 1px solid #d5e5dd;
            color: #333;
            vertical-align: middle;
        }

        .invoice-box tbody tr:nth-child(even) {
            background-color: #fbfdfc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .total-row {
            background-color: #f7faf8 !important;
            font-weight: bold;
        }

        .grand-total-label {
            background-color: #cfece0 !important;
            color: #0c4b33 !important;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #b8dfd0 !important;
            text-align: right;
        }

        .grand-total-amount {
            background-color: #f7faf8 !important;
            color: #0c4b33 !important;
            font-size: 12.5px;
            font-weight: bold;
            border: 1px solid #b8dfd0 !important;
            text-align: right;
        }

        .declaration-signatory {
            width: 100%;
            margin-top: 14px;
            page-break-inside: avoid;
            border: 1px solid #d0e4dc;
            border-collapse: collapse;
            background-color: #f7faf8;
        }

        .declaration-signatory td {
            width: 50%;
            padding: 6px 10px;
            vertical-align: top;
        }

        .declaration-cell {
            border-right: 1px solid #d0e4dc;
            font-size: 10px;
            color: #555;
            line-height: 1.4;
        }

        .signatory-cell {
            height: 70px;
            text-align: right;
            vertical-align: top !important;
        }

        .signatory-company {
            font-size: 10px;
            color: #0c4b33;
            text-align: right;
            font-weight: bold;
        }

        .signatory-bottom {
            font-size: 10px;
            color: #0c4b33;
            text-align: right;
            margin-top: 35px;
        }
    </style>
</head>

<body>
    @php
        $displayVal = function($val) {
            $trimmed = trim((string)($val ?? ''));
            return ($trimmed === '' || $trimmed === '-' || $trimmed === '--') ? 'N/A' : $val;
        };

        $displayClinicName = $clinic_name ?? \App\Models\Setting::where('key', 'clinic_name')->value('value') ?? 'Srinethi Clinic & Lakshmi Lab';
        
        $resolvedLogoPath = null;
        $candidateLogos = array_filter([
            $clinic_logo ?? null,
            \App\Models\Setting::where('key', 'clinic_logo')->value('value'),
            'admin/assets/img/cliniclogo.png',
            'admin/assets/img/cliniclogohalf.png',
            'admin/assets/img/logo.png',
        ]);

        foreach ($candidateLogos as $candidate) {
            if (empty($candidate) || $candidate === 'default-logo.png') continue;
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

        $displayClinicPhone = $clinic_phone ?? \App\Models\Setting::where('key', 'clinic_phone')->value('value');
        $displayClinicEmail = $clinic_email ?? \App\Models\Setting::where('key', 'clinic_email')->value('value');
        $displayClinicAddress = $clinic_address ?? \App\Models\Setting::where('key', 'clinic_address')->value('value');
        $displayClinicCity = $clinic_city ?? \App\Models\Setting::where('key', 'clinic_city')->value('value');
        $displayClinicState = $clinic_state ?? \App\Models\Setting::where('key', 'clinic_state')->value('value');
        $addressParts = array_filter([$displayClinicAddress ?? '', $displayClinicCity ?? '', $displayClinicState ?? '']);
    @endphp

    <div class="invoice-wrapper">
        <!-- Clinic Header -->
        <table class="header-table">
            <tr>
                <td style="width: 40%; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Clinic Logo" style="max-height: 60px; max-width: 190px;">
                    @elseif(!empty($resolvedLogoPath) && file_exists($resolvedLogoPath))
                        <img src="{{ $resolvedLogoPath }}" alt="Clinic Logo" style="max-height: 60px; max-width: 190px;">
                    @else
                        <div class="clinic-title">{{ $displayClinicName }}</div>
                    @endif
                </td>
                <td style="width: 60%; text-align: right; vertical-align: middle;">
                    <div class="clinic-title">{{ $displayClinicName }}</div>
                    @if(!empty($addressParts))
                        <div class="clinic-sub">{{ implode(', ', $addressParts) }}</div>
                    @endif
                    <div class="clinic-sub">
                        @if(!empty($displayClinicPhone))PHONE: {{ $displayClinicPhone }}@endif
                        @if(!empty($displayClinicPhone) && !empty($displayClinicEmail)) | @endif
                        @if(!empty($displayClinicEmail))EMAIL: {{ $displayClinicEmail }}@endif
                    </div>
                </td>
            </tr>
        </table>

        <hr class="header-divider">

        <!-- Statement & Entity Details -->
        <table class="details-container">
            <tr>
                <!-- Entity Info -->
                <td class="details-box details-box-left">
                    <div class="section-heading">{{ strtoupper($type) }} DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Name</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($entity['name'] ?? '') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($entity['phone'] ?? '') }}</td>
                        </tr>
                        @if(!empty($entity['email']) && $entity['email'] !== 'N/A')
                        <tr>
                            <td class="info-label">Email</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($entity['email']) }}</td>
                        </tr>
                        @endif
                        @if(!empty($entity['gstin']) && $entity['gstin'] !== 'N/A')
                        <tr>
                            <td class="info-label">GSTIN</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($entity['gstin']) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($entity['address'] ?? '') }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Statement Meta Info -->
                <td class="details-box details-box-right">
                    <div class="section-heading">STATEMENT DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Statement Type</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ ucfirst($type) }} Account Ledger</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Period</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $period ?? 'All Time' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Generated Date</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ date('d-M-Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Opening Balance</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">₹{{ number_format((float)$opening_balance, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Closing Balance</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong style="color: #0c4b33;">₹{{ number_format((float)$closing_balance, 2) }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Banner -->
        <div class="section-banner">CHRONOLOGICAL ACCOUNT LEDGER STATEMENT</div>

        <!-- Ledger Entries Table -->
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 32%; text-align: left;">Particulars</th>
                    <th style="width: 12%;">Vch Type</th>
                    <th style="width: 12%;">Vch No</th>
                    <th style="width: 10%; text-align: right;">Debit (₹)</th>
                    <th style="width: 10%; text-align: right;">Credit (₹)</th>
                    <th style="width: 12%; text-align: right;">Balance (₹)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance -->
                <tr style="background-color: #f7faf8; font-weight: bold;">
                    <td class="text-center">-</td>
                    <td class="text-left"><strong>Opening Balance</strong></td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">₹{{ number_format((float)$opening_balance, 2) }}</td>
                </tr>

                @php
                    $sumDebit = 0;
                    $sumCredit = 0;
                @endphp

                @forelse($ledger_entries as $entry)
                    @php
                        $d = (float)$entry['debit'];
                        $c = (float)$entry['credit'];
                        $sumDebit += $d;
                        $sumCredit += $c;
                    @endphp
                    <tr>
                        <td class="text-center">{{ date('d-M-Y', strtotime($entry['date'])) }}</td>
                        <td class="text-left">{{ $entry['particulars'] }}</td>
                        <td class="text-center">{{ $entry['vch_type'] }}</td>
                        <td class="text-center">{{ $entry['vch_no'] }}</td>
                        <td class="text-right">{{ $d > 0 ? number_format($d, 2) : '-' }}</td>
                        <td class="text-right">{{ $c > 0 ? number_format($c, 2) : '-' }}</td>
                        <td class="text-right fw-bold">₹{{ number_format((float)$entry['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px; color: #777;">No transaction entries found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right fw-bold">Total Transactions:</td>
                    <td class="text-right fw-bold">₹{{ number_format($sumDebit, 2) }}</td>
                    <td class="text-right fw-bold">₹{{ number_format($sumCredit, 2) }}</td>
                    <td class="text-right fw-bold">-</td>
                </tr>
                <tr>
                    <td colspan="4" class="grand-total-label">Closing Outstanding Balance:</td>
                    <td colspan="3" class="grand-total-amount">₹{{ number_format((float)$closing_balance, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatory -->
        <table class="declaration-signatory">
            <tr>
                <td class="declaration-cell">
                    <strong>Note:</strong> This is a computer-generated account ledger statement. For any discrepancies or reconciliation questions, please contact our accounts department within 7 business days.
                </td>
                <td class="signatory-cell">
                    <div class="signatory-company">For {{ $displayClinicName }}</div>
                    <div class="signatory-bottom">Authorized Signatory</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
