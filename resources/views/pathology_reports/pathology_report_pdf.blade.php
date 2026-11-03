<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pathology Report</title>
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
            width: 115px;
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

        .report-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            background-color: #fff;
            margin-top: 8px;
        }

        .report-table td {
            padding: 10px 12px;
            font-size: 11.5px;
            line-height: 1.6;
            color: #333;
            vertical-align: top;
        }

        .declaration-signatory {
            width: 100%;
            margin-top: 18px;
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

        <!-- Patient and Report Overview Box -->
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
                            <td class="info-label">Gender / Age</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">
                                {{ $displayVal($patient->gender ? ucfirst($patient->gender) : '') }}
                                @if(!empty($patient->dob))
                                    / {{ \Carbon\Carbon::parse($patient->dob)->age }} yrs
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->address ? ucfirst($patient->address) : '') }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Report Overview -->
                <td class="details-box details-box-right">
                    <div class="section-heading">PATHOLOGY REPORT OVERVIEW:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Report #</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>#LAB-{{ $report->id ?? '---' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Test Name</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($test->test_name ?? 'Pathology Test') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Test Code</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($test->test_code ?? '---') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Sample Collected</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($report->sample_collected_date ? \Carbon\Carbon::parse($report->sample_collected_date)->format('d-m-Y') : '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Report Date</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') : $date) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Laboratory Investigation Details Table -->
        <div class="section-banner">
            LABORATORY INVESTIGATION SPECIFICATIONS
        </div>

        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Test Code</th>
                    <th style="width: 28%;">Investigation / Test Name</th>
                    <th style="width: 16%;">Sample Type</th>
                    <th style="width: 18%;">Normal Reference Range</th>
                    <th style="width: 18%;">Result / Finding</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><strong>{{ $displayVal($test->test_code ?? '---') }}</strong></td>
                    <td><strong>{{ $displayVal($test->test_name ?? 'Pathology Test') }}</strong></td>
                    <td>{{ $displayVal($test->sample_type ?? 'Blood / Serum') }}</td>
                    <td>{{ $displayVal($test->normal_range ?? 'Standard') }}</td>
                    <td>
                        <strong style="color: {{ strtolower($report->result ?? '') === 'positive' ? '#b91c1c' : '#047857' }};">
                            {{ $displayVal($report->result ? ucfirst($report->result) : '') }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Clinical Result & Observations -->
        <div class="section-banner" style="margin-top: 14px;">
            LABORATORY FINDINGS & TEST SPECIFICATIONS
        </div>

        <table class="report-table">
            <tr>
                <td>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 0 6px 0 0;">
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Test Name</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val"><strong>{{ $displayVal($test->test_name ?? '') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Test Code</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val"><strong>{{ $displayVal($test->test_code ?? '---') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Sample Type</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val">{{ $displayVal($test->sample_type ?? '') }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; vertical-align: top; padding: 0 0 0 6px;">
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Normal Range</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val">{{ $displayVal($test->normal_range ?? '') }}</td>
                                    </tr>
                                    @if(!empty($test->cost))
                                    <tr>
                                        <td class="info-label">Test Cost</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val">₹{{ number_format((float)$test->cost, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($test->report_format))
                                    <tr>
                                        <td class="info-label">Report Format</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val">{{ $test->report_format }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="info-label">Result Finding</td>
                                        <td class="info-sep">:</td>
                                        <td class="info-val">
                                            <strong style="color: {{ strtolower($report->result ?? '') === 'positive' ? '#b91c1c' : '#047857' }};">
                                                {{ $displayVal($report->result ? ucfirst($report->result) : '') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    @if(!empty($report->result) && strlen($report->result) > 15)
                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #d0e4dc;">
                            <strong style="color: #0c4b33;">Clinical Remarks & Observations:</strong><br>
                            <div style="margin-top: 3px;">
                                {!! nl2br(e($report->result)) !!}
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 10px; font-size: 10px; color: #6b7280; font-style: italic;">
                        Note: Interpretation of laboratory results should always be correlated clinically by the attending physician along with patient history and physical examination.
                    </div>
                </td>
            </tr>
        </table>

        <!-- Declaration & Authorized Signatory -->
        <table class="declaration-signatory">
            <tr>
                <td class="declaration-cell">
                    <div style="font-size: 10.5px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                        Pathology Quality Assurance:
                    </div>
                    <div style="font-size: 10px; color: #444; line-height: 1.4;">
                        All laboratory tests are performed under calibrated instruments and standardized protocols. This report is verified and electronically certified.
                    </div>
                </td>
                <td class="signatory-cell">
                    <div class="signatory-company">For, {{ $displayClinicName }}</div>
                    <div class="signatory-bottom">Pathologist / Authorized Laboratory Incharge</div>
                </td>
            </tr>
        </table>

        <!-- Footer Note -->
        <div class="footer-note">
            For more information or inquiries regarding this laboratory report, contact 
            <strong>{{ $displayClinicPhone ?? '9876543210' }}</strong> 
            or email <strong>{{ $displayClinicEmail ?? 'clinic@example.com' }}</strong>.
        </div>

    </div>

</body>

</html>
