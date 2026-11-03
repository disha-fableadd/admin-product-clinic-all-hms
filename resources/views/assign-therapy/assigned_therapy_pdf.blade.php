<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assigned Therapy</title>
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
            font-size: 12px;
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
            position: relative;
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
            margin: 12px 0 14px 0;
        }

        .details-container {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            background-color: #f7faf8;
        }

        .details-box {
            padding: 9px 12px;
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
            font-size: 11.5px;
            font-weight: bold;
            color: #0c4b33;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #c4ddd1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
        }

        .info-table td,
        .info-table th {
            padding: 2.5px 0;
            vertical-align: top;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
        }

        .info-label {
            width: 110px;
            color: #4b5563;
            font-weight: normal;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .info-sep {
            width: 12px;
            color: #4b5563;
            text-align: center;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .info-val {
            color: #1f2937;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .section-banner {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 12.5px;
            font-weight: bold;
            text-align: center;
            padding: 6px 0;
            letter-spacing: 1px;
            margin-top: 14px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
        }

        .invoice-box th {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: bold;
            text-align: center !important;
            padding: 7px 8px;
            border: 1px solid #b8dfd0;
            font-size: 11.5px;
            letter-spacing: 0.3px;
            vertical-align: middle !important;
        }

        .invoice-box td {
            padding: 7px 8px;
            border: 1px solid #d5e5dd;
            color: #333;
            font-size: 11.5px;
            vertical-align: middle !important;
            text-align: center !important;
        }

        .invoice-box tbody tr:nth-child(even) {
            background-color: #fbfdfc;
        }

        .declaration-signatory {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
            border: 1px solid #d0e4dc;
            border-collapse: collapse;
            background-color: #f7faf8;
            table-layout: fixed;
        }

        .declaration-signatory td {
            padding: 10px;
            vertical-align: top;
        }

        .declaration-cell {
            width: 50%;
            border-right: 1px solid #d0e4dc;
        }

        .signatory-cell {
            width: 50%;
            position: relative;
            height: 95px;
            vertical-align: top !important;
        }

        .signatory-company {
            text-align: right;
            font-size: 10.5px;
            color: #0c4b33;
            font-weight: bold;
        }

        .signatory-bottom {
            position: absolute;
            bottom: 10px;
            right: 10px;
            text-align: right;
            font-size: 10px;
            color: #0c4b33;
        }

        .footer-note {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #c0d8cd;
            text-align: center;
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    @php
        $displayVal = function($val) {
            $trimmed = trim((string)($val ?? ''));
            return ($trimmed === '' || $trimmed === '-' || $trimmed === '--') ? 'N/A' : $val;
        };

        $displayClinicName = $clinic_name ?? \App\Models\Setting::where('key', 'clinic_name')->value('value') ?? 'Sunshine Clinic';

        // Comprehensive logo resolution
        $resolvedLogoPath = null;
        $candidateLogos = array_filter([
            $clinic_logo ?? null,
            \App\Models\Setting::where('key', 'clinic_logo')->value('value'),
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

        // Convert to Base64 Data URI for 100% reliable DomPDF rendering
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

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 40%; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Clinic Logo" style="max-height: 65px; max-width: 190px;">
                    @elseif(!empty($resolvedLogoPath) && file_exists($resolvedLogoPath))
                        <img src="{{ $resolvedLogoPath }}" alt="Clinic Logo" style="max-height: 65px; max-width: 190px;">
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

        <!-- Patient and Therapy Overview Box -->
        <table class="details-container">
            <tr>
                <!-- Patient Details -->
                <td class="details-box details-box-left">
                    <div class="section-heading">PATIENT DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Name</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($patient->fullname ? ucfirst($patient->fullname) : '') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->phone ?? '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->address ? ucfirst($patient->address) : '') }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Therapy Overview -->
                <td class="details-box details-box-right">
                    <div class="section-heading">THERAPY OVERVIEW:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Record #</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>#{{ $assignedTherapy->id ?? '---' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Date</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($date) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Status</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($assignedTherapy->status ? ucfirst($assignedTherapy->status) : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Assigned Therapy Details Table -->
        <div class="section-banner">
            ASSIGNED THERAPY DETAILS
        </div>

        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 26%;">Therapy Name</th>
                    <th style="width: 24%;">Doctor</th>
                    <th style="width: 16%;">Type</th>
                    <th style="width: 17%;">Start Date</th>
                    <th style="width: 17%;">End Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $displayVal($therapy->name ?? '') }}</strong></td>
                    <td>{{ $displayVal($doctor->fullname ? ucfirst($doctor->fullname) : '') }}</td>
                    <td>{{ $displayVal($assignedTherapy->type ? ucfirst($assignedTherapy->type) : '') }}</td>
                    <td>{{ $displayVal($assignedTherapy->start_date ?? '') }}</td>
                    <td>{{ $displayVal($assignedTherapy->end_date ?? '') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Declaration & Authorized Signatory -->
        <table class="declaration-signatory">
            <tr>
                <td class="declaration-cell">
                    <div style="font-size: 10.5px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                        Patient Instructions:
                    </div>
                    <div style="font-size: 10px; color: #444; line-height: 1.4;">
                        Please adhere strictly to the prescribed therapy schedule and instructions given by your therapist. If you experience any discomfort or need to reschedule, notify the clinic staff immediately.
                    </div>
                </td>
                <td class="signatory-cell">
                    <div class="signatory-company">For, {{ $displayClinicName }}</div>
                    <div class="signatory-bottom">Authorized Signatory</div>
                </td>
            </tr>
        </table>

        <!-- Footer Note -->
        <div class="footer-note">
            For more information or queries, contact us at 
            <strong>{{ $displayClinicPhone ?? '9876543210' }}</strong> 
            or email <strong>{{ $displayClinicEmail ?? 'clinic@example.com' }}</strong>.
        </div>

    </div>

</body>

</html>