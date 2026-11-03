<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Patient Full History</title>
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
            text-align: left;
            padding: 5px 8px;
            letter-spacing: 0.5px;
            margin-top: 14px;
            margin-bottom: 6px;
            background-color: #eef6f2;
            border-left: 3.5px solid #0c4b33;
            text-transform: uppercase;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 8px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
        }

        .invoice-box th {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: bold;
            text-align: center !important;
            padding: 7px 6px;
            border: 1px solid #b8dfd0;
            font-size: 11.5px;
            letter-spacing: 0.3px;
            vertical-align: middle !important;
        }

        .invoice-box td {
            padding: 6px;
            border: 1px solid #d5e5dd;
            color: #333;
            font-size: 11px;
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
        $displayClinicCity = \App\Models\Setting::where('key', 'clinic_city')->value('value');
        $displayClinicState = \App\Models\Setting::where('key', 'clinic_state')->value('value');
        $addressParts = array_filter([$displayClinicAddress ?? '', $displayClinicCity ?? '', $displayClinicState ?? '']);

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
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

        <!-- Patient Details 2-Column Info Box -->
        <table class="details-container">
            <tr>
                <td class="details-box details-box-left">
                    <div class="section-heading">PATIENT INFORMATION:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Patient ID</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($patient->patient_unique_id ?? '') }}</strong></td>
                        </tr>
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
                        @if($currentProjectTypeId !== 3)
                        <tr>
                            <td class="info-label">Email</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->email ?? '') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->address ? ucfirst($patient->address) : '') }}</td>
                        </tr>
                    </table>
                </td>

                <td class="details-box details-box-right">
                    <div class="section-heading">ADDITIONAL DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">City / State</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal(implode(', ', array_filter([$patient->city ?? '', $patient->state ?? '']))) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Age / DOB</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal(implode(' | ', array_filter([$patient->age ? $patient->age . ' yrs' : '', $patient->birthdate ?? '']))) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Referral Source</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->referral_source ?? '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Symptom Remarks</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->symptom_remarks ?? '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Report Date</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($date) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Followup History -->
        <div class="section-banner">Followup History</div>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 30%;">Doctor</th>
                    @if($currentProjectTypeId !== 3)
                        <th style="width: 30%;">Treatment</th>
                    @endif
                    <th style="width: 20%;">Date</th>
                    @if($currentProjectTypeId !== 3)
                        <th style="width: 20%;">Type</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($followups as $f)
                    <tr>
                        <td>{{ $displayVal($f->doctor->fullname ?? '') }}</td>
                        @if($currentProjectTypeId !== 3)
                            <td>{{ $displayVal($f->treatment->name ?? '') }}</td>
                        @endif
                        <td>{{ $displayVal($f->date ?? '') }}</td>
                        @if($currentProjectTypeId !== 3)
                            <td>{{ $displayVal($f->followup_type ? ucfirst($f->followup_type) : '') }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $currentProjectTypeId !== 3 ? 4 : 2 }}">No followup history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Appointments History -->
        <div class="section-banner">Appointments History</div>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 26%;">Doctor</th>
                    <th style="width: 26%;">Treatment</th>
                    <th style="width: 20%;">Date</th>
                    @if($currentProjectTypeId !== 3)
                        <th style="width: 14%;">Type</th>
                        <th style="width: 14%;">Status</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                    <tr>
                        <td>{{ $displayVal($a->doctor->fullname ?? '') }}</td>
                        <td>{{ $displayVal($a->treatment->name ?? '') }}</td>
                        <td>{{ $displayVal($a->date ?? '') }}</td>
                        @if($currentProjectTypeId !== 3)
                            <td>{{ $displayVal($a->appoint_type ? ucfirst($a->appoint_type) : '') }}</td>
                            <td>{{ $displayVal($a->status ? ucfirst($a->status) : '') }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $currentProjectTypeId !== 3 ? 5 : 3 }}">No appointments history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Payment History -->
        <div class="section-banner">Payment History</div>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 22%;">Date</th>
                    <th style="width: 18%;">Total Amount</th>
                    <th style="width: 18%;">Paid Amount</th>
                    <th style="width: 18%;">Remaining</th>
                    <th style="width: 24%;">Collected By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ $displayVal($p->dailyData->date ? \Carbon\Carbon::parse($p->dailyData->date)->format('d-m-Y') : '') }}</td>
                        <td>{{ $displayVal($p->amount ?? '0.00') }}</td>
                        <td>{{ $displayVal($p->paid_amount ?? '0.00') }}</td>
                        <td>{{ $displayVal($p->remain_amount ?? '0.00') }}</td>
                        <td>{{ $displayVal($p->dailyData->collectedBy->fullname ?? '') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No payment history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Report History -->
        <div class="section-banner">Medical Reports History</div>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 20%;">Date</th>
                    <th style="width: 25%;">Report Type</th>
                    <th style="width: 55%;">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td>{{ $displayVal($r->date ?? '') }}</td>
                        <td>{{ $displayVal($r->report_type ? ucfirst($r->report_type) : '') }}</td>
                        <td>{{ $displayVal($r->description ?? '') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No report history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Treatment History -->
        <div class="section-banner">Treatment Booking History</div>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 24%;">Doctor</th>
                    <th style="width: 24%;">Treatment</th>
                    <th style="width: 16%;">Plan</th>
                    <th style="width: 20%;">Payment Date</th>
                    <th style="width: 16%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($treatmentPayments as $tp)
                    <tr>
                        <td>{{ $displayVal($tp->booking->treatment->doctor->fullname ?? '') }}</td>
                        <td>{{ $displayVal($tp->booking->treatment->name ?? '') }}</td>
                        <td>{{ $displayVal($tp->booking->plan ? ucfirst($tp->booking->plan) : '') }}</td>
                        <td>{{ $displayVal($tp->booking->payment_date ? \Carbon\Carbon::parse($tp->booking->payment_date)->format('d-m-Y') : '') }}</td>
                        <td>{{ $displayVal($tp->booking->status ? ucfirst($tp->booking->status) : '') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No treatment history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Declaration & Authorized Signatory -->
        <table class="declaration-signatory">
            <tr>
                <td class="declaration-cell">
                    <div style="font-size: 10.5px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                        Comprehensive Medical Record:
                    </div>
                    <div style="font-size: 10px; color: #444; line-height: 1.4;">
                        This document is a confidential summary of the patient's complete history at this clinic, including sessions, consultations, and payment accounts.
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
