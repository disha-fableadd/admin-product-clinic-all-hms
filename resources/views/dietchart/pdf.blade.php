<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Diet Chart - {{ $dietChart->name ?? 'Diet Chart' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #222;
            font-size: 11px;
            line-height: 1.4;
            background-color: #fff;
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

        /* Clinic & Document Header */
        .clinic-header {
            width: 100%;
            border-collapse: collapse;
        }

        .clinic-header td {
            vertical-align: top;
            padding: 0;
        }

        .clinic-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a4d36;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .clinic-info {
            font-size: 10px;
            line-height: 1.35;
            color: #444;
        }

        .doc-title-badge {
            font-size: 16px;
            font-weight: 700;
            color: #0c4b33;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #cfece0;
            padding: 4px 14px;
            border: 1px solid #b8dfd0;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .header-meta {
            font-size: 10.5px;
            line-height: 1.45;
            color: #333;
        }

        .header-divider {
            border-top: 1.5px solid #222;
            margin: 12px 0 14px 0;
        }

        /* Chart Banner */
        .chart-banner {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            background: #f7faf8;
            margin-bottom: 14px;
        }

        .chart-banner td {
            padding: 7px 10px;
        }

        .chart-banner .banner-title {
            font-size: 12px;
            font-weight: 700;
            color: #0c4b33;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .chart-banner .banner-meta {
            font-size: 10.5px;
            color: #444;
            text-align: right;
        }

        /* Entry Cards */
        .entry-card {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            margin-bottom: 12px;
            background: #fff;
            page-break-inside: avoid;
        }

        .entry-card-header {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: 700;
            font-size: 11.5px;
            padding: 6px 10px;
            border-bottom: 1px solid #b8dfd0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .entry-card-body td {
            padding: 10px;
            vertical-align: top;
        }

        .entry-image-td {
            width: 130px;
            text-align: center;
        }

        .entry-image {
            width: 120px;
            height: 120px;
            border-radius: 4px;
            border: 1px solid #b8dfd0;
            object-fit: cover;
        }

        .entry-text-td {
            padding-left: 12px;
        }

        .entry-heading {
            font-size: 12.5px;
            font-weight: 700;
            color: #1a4d36;
            margin-bottom: 4px;
        }

        .entry-underline {
            display: block;
            width: 45px;
            height: 2px;
            background: #1a4d36;
            margin-bottom: 8px;
        }

        .entry-desc {
            font-size: 10.5px;
            line-height: 1.5;
            color: #333;
            text-align: justify;
            margin-bottom: 8px;
        }

        .entry-time-badge {
            display: inline-block;
            background: #eaf5f0;
            color: #0c4b33;
            border: 1px solid #b8dfd0;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
        }

        /* Declaration & Signatory Box */
        .declaration-signatory {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #222;
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .declaration-signatory td {
            vertical-align: top;
            padding: 8px 10px;
        }

        .declaration-pane {
            width: 60%;
            border-right: 1px solid #222;
            font-size: 9.5px;
            line-height: 1.4;
            color: #444;
        }

        .declaration-title {
            font-weight: 700;
            color: #1a4d36;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 10px;
        }

        .signatory-pane {
            width: 40%;
            text-align: right;
            padding-top: 8px;
        }

        .signatory-company {
            font-size: 10.5px;
            font-weight: 700;
            color: #111;
            margin-bottom: 35px;
        }

        .signatory-label {
            font-size: 10px;
            font-weight: 700;
            color: #333;
            border-top: 1px dashed #666;
            display: inline-block;
            padding-top: 4px;
            min-width: 140px;
            text-align: center;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            font-size: 9.5px;
            color: #666;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    @php
        $logoBase64 = null;
        $logoPathCandidate = null;

        if (!empty($settings['clinic_logo'])) {
            $logoPathCandidate = public_path($settings['clinic_logo']);
            if (!file_exists($logoPathCandidate)) {
                $logoPathCandidate = public_path('storage/' . $settings['clinic_logo']);
            }
        }

        if ($logoPathCandidate && file_exists($logoPathCandidate)) {
            $logoData = @file_get_contents($logoPathCandidate);
            if ($logoData) {
                $mime = mime_content_type($logoPathCandidate) ?: 'image/png';
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($logoData);
            }
        }

        $resolveImage = function($imgRel) {
            if (empty($imgRel)) return null;
            $candidates = [
                public_path('storage/' . $imgRel),
                public_path($imgRel),
                public_path('uploads/' . $imgRel),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_file($cand)) {
                    $c = @file_get_contents($cand);
                    if ($c) {
                        $m = mime_content_type($cand) ?: 'image/jpeg';
                        return 'data:' . $m . ';base64,' . base64_encode($c);
                    }
                }
            }
            return null;
        };
    @endphp

    <div class="invoice-wrapper">
        <!-- Clinic & Document Header -->
        <table class="clinic-header">
            <tr>
                <td style="width: 60%;">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Clinic Logo" style="height: 52px; max-width: 140px; margin-bottom: 6px; object-fit: contain;">
                    @endif
                    <div class="clinic-title">{{ $settings['clinic_name'] ?? 'Clinic Name' }}</div>
                    <div class="clinic-info">
                        {{ $settings['clinic_address'] ?? 'Clinic Address' }}<br>
                        @if (!empty($settings['clinic_city']) || !empty($settings['clinic_state']))
                            {{ $settings['clinic_city'] ?? '' }}{{ !empty($settings['clinic_city']) && !empty($settings['clinic_state']) ? ', ' : '' }}{{ $settings['clinic_state'] ?? '' }}
                            {{ !empty($settings['clinic_pincode']) ? ' - ' . $settings['clinic_pincode'] : '' }}<br>
                        @endif
                        <strong>Phone:</strong> {{ $settings['clinic_phone'] ?? '---' }} &nbsp;|&nbsp; <strong>Email:</strong> {{ $settings['clinic_email'] ?? '---' }}
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="doc-title-badge">DIET CHART</div>
                    <div class="header-meta">
                        <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}<br>
                        <strong>Diet Chart ID:</strong> #{{ $dietChart->id ?? '---' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="header-divider"></div>

        <!-- Chart Banner -->
        <table class="chart-banner">
            <tr>
                <td class="banner-title">
                    Diet Plan: {{ $dietChart->name ?? 'Standard Diet Chart' }}
                </td>
                <td class="banner-meta">
                    <strong>Total Meals/Items:</strong> {{ count($titles ?? []) }}
                </td>
            </tr>
        </table>

        <!-- Diet Entries -->
        @if (!empty($titles) && count($titles) > 0)
            @foreach ($titles as $i => $title)
                @php
                    $imgData = isset($images[$i]) ? $resolveImage($images[$i]) : null;
                    if (!$imgData && $logoBase64) {
                        $imgData = $logoBase64;
                    }
                @endphp
                <table class="entry-card">
                    <tr>
                        <td colspan="{{ $imgData ? '2' : '1' }}" class="entry-card-header">
                            Meal {{ $i + 1 }}: {{ ucfirst($title) }}
                        </td>
                    </tr>
                    <tr class="entry-card-body">
                        @if ($imgData)
                            <td class="entry-image-td">
                                <img src="{{ $imgData }}" alt="Meal Image" class="entry-image">
                            </td>
                        @endif
                        <td class="entry-text-td">
                            <div class="entry-heading">{{ ucfirst($title) }}</div>
                            <span class="entry-underline"></span>
                            <div class="entry-desc">
                                {{ $descriptions[$i] ?? 'No instructions specified.' }}
                            </div>
                            <div class="entry-time-badge">
                                <strong>Recommended Time:</strong> {{ $times[$i] ?? '-' }}
                            </div>
                        </td>
                    </tr>
                </table>
            @endforeach
        @else
            <div style="padding: 16px; text-align: center; color: #777; border: 1px solid #d0e4dc; background: #fafafa; border-radius: 4px; margin-bottom: 12px;">
                No diet plan entries recorded.
            </div>
        @endif

        <!-- Declaration & Signatory Box -->
        <table class="declaration-signatory">
            <tr>
                <td class="declaration-pane">
                    <div class="declaration-title">Dietary Guidelines & Support:</div>
                    Please strictly follow the timing and nutritional advice provided above. For any dietary concerns, allergies, or questions, contact our nutritionist team at <strong>{{ $settings['clinic_email'] ?? 'clinic@example.com' }}</strong> or call <strong>{{ $settings['clinic_phone'] ?? '---' }}</strong>.
                </td>
                <td class="signatory-pane">
                    <div class="signatory-company">For, {{ $settings['clinic_name'] ?? 'Clinic Management' }}</div>
                    <div class="signatory-label">Authorized Dietitian / Doctor</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-note">
            This is an official clinic nutritional advisory chart. Valid without physical signature when issued electronically.
        </div>
    </div>
</body>
</html>
