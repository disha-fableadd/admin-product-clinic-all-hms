<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tax Invoice / Receipt - {{ $order['invoice_no'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #111;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .outer-wrapper {
            border: 1.8px solid #94a3b8;
            border-radius: 6px;
            padding: 12px 14px;
            background: #ffffff;
        }
        
        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-logo {
            max-height: 48px;
            max-width: 150px;
        }
        .company-title {
            font-size: 13.5px;
            font-weight: bold;
            color: #1f4e43;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 3px;
            text-align: right;
        }
        .company-address {
            font-size: 9px;
            color: #334155;
            line-height: 1.35;
            text-align: right;
        }
        .company-contact {
            font-size: 8.5px;
            font-weight: bold;
            color: #1f4e43;
            margin-top: 3px;
            text-align: right;
        }

        /* Two columns customer and order details */
        .info-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-box {
            width: 48.5%;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 7px 10px;
            vertical-align: top;
            background: #ffffff;
        }
        .info-spacer {
            width: 3%;
        }
        .box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1f4e43;
            border-bottom: 2px solid #2e7d6b;
            padding-bottom: 3px;
            margin-bottom: 5px;
            letter-spacing: 0.4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            font-size: 9px;
        }
        .lbl {
            width: 35%;
            color: #475569;
            font-weight: 500;
        }
        .sep {
            width: 4%;
            text-align: center;
            color: #64748b;
        }
        .val {
            width: 61%;
            color: #0f172a;
            font-weight: 600;
        }

        /* Section Title with lines */
        .section-header-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 10px 0;
        }
        .section-header-line {
            border-top: 1px solid #94a3b8;
            height: 1px;
        }
        .section-header-text {
            width: 1%;
            white-space: nowrap;
            padding: 0 12px;
            font-size: 11px;
            font-weight: bold;
            color: #1f4e43;
            letter-spacing: 0.6px;
        }

        /* Plan Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th {
            background-color: #2e7d6b;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 5px;
            text-align: center;
            border: 1px solid #2e7d6b;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        .items-table td {
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .plan-title {
            font-size: 11px;
            font-weight: bold;
            color: #1f4e43;
            margin-bottom: 2px;
        }
        .plan-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .plan-dates {
            font-size: 8.5px;
            color: #2e7d6b;
            font-weight: bold;
        }

        /* Total bar */
        .total-container {
            width: 100%;
            margin-bottom: 12px;
            text-align: right;
        }
        .total-label {
            display: inline-block;
            font-size: 11px;
            font-weight: bold;
            color: #1f4e43;
            text-transform: uppercase;
            margin-right: 12px;
            vertical-align: middle;
        }
        .total-box {
            display: inline-block;
            background: #2e7d6b;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: bold;
            padding: 6px 20px;
            border-radius: 4px;
            vertical-align: middle;
        }

        /* Featured modules */
        .modules-card {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 12px;
            background: #ffffff;
        }
        .modules-title {
            font-size: 10px;
            font-weight: bold;
            color: #1f4e43;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 2px solid #2e7d6b;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        .modules-title-icon {
            display: inline-block;
            background: #2e7d6b;
            color: #ffffff;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 8.5px;
            font-weight: bold;
            margin-right: 4px;
            vertical-align: middle;
        }
        .modules-table {
            width: 100%;
            border-collapse: collapse;
        }
        .modules-table td {
            width: 33.33%;
            padding: 2.5px 0;
            font-size: 9px;
            color: #1e293b;
            vertical-align: middle;
        }
        .module-bullet {
            color: #2e7d6b;
            font-size: 12px;
            line-height: 0;
            margin-right: 4px;
            display: inline-block;
        }

        /* Bottom section (terms & signatory) */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
        }
        .terms-box {
            width: 58%;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 8px 10px;
            vertical-align: top;
            background: #ffffff;
        }
        .bottom-spacer {
            width: 4%;
        }
        .sign-box {
            width: 38%;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 8px 10px;
            vertical-align: top;
            text-align: center;
            background: #ffffff;
        }
        .terms-ol {
            margin: 0;
            padding-left: 13px;
            font-size: 8px;
            color: #334155;
            line-height: 1.4;
        }
        .terms-ol li {
            margin-bottom: 2.5px;
        }
        .sign-space {
            height: 35px;
        }
        .sign-img {
            max-height: 32px;
            margin-bottom: 2px;
        }
        .sign-line {
            border-top: 1px solid #334155;
            width: 80%;
            margin: 0 auto 4px auto;
        }
        .sign-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #1f4e43;
            text-transform: uppercase;
        }
        .sign-sub {
            font-size: 7.5px;
            color: #64748b;
        }
        .rupee {
            font-family: 'DejaVu Sans', sans-serif !important;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="outer-wrapper">
        
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 30%;">
                    @php
                        $localLogoPath = public_path('admin/assets/img/logo/Fablead_HMS_4K.png');
                    @endphp
                    @if(!empty($company['logo_url']))
                        <img src="{{ $company['logo_url'] }}" class="company-logo" alt="Logo">
                    @elseif(file_exists($localLogoPath))
                        <img src="{{ $localLogoPath }}" class="company-logo" alt="Fablead HMS">
                    @else
                        <div style="font-size: 20px; font-weight: bold; color: #2e7d6b;">
                            <span style="color: #1f4e43;">Fab</span>lead HMS
                        </div>
                    @endif
                </td>
                <td style="width: 70%; text-align: right;">
                    <div class="company-title">{{ $company['company_name'] }}</div>
                    <div class="company-address">{{ $company['address'] }}</div>
                    <div class="company-contact">
                        PHONE: {{ $company['contact'] }} &nbsp;|&nbsp; EMAIL: {{ $company['email'] }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Customer & Order details -->
        <table class="info-container">
            <tr>
                <!-- Customer Details -->
                <td class="info-box">
                    <div class="box-title">CUSTOMER DETAILS</div>
                    <table class="info-table">
                        <tr>
                            <td class="lbl">Name</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $customer['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Clinic / Org</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $customer['company'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Phone</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $customer['phone'] ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Email</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $customer['email'] ?: '-' }}</td>
                        </tr>
                    </table>
                </td>

                <td class="info-spacer"></td>

                <!-- Order Details -->
                <td class="info-box">
                    <div class="box-title">ORDER DETAILS</div>
                    <table class="info-table">
                        <tr>
                            <td class="lbl">Order Status</td>
                            <td class="sep">:</td>
                            <td class="val" style="color: #16a34a; font-weight: bold;">{{ $order['status'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Invoice No</td>
                            <td class="sep">:</td>
                            <td class="val" style="font-weight: bold;">{{ $order['invoice_no'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Order ID</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $order['order_id'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Order Date</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $order['date'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Payment Method</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $order['payment_method'] }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Validity</td>
                            <td class="sep">:</td>
                            <td class="val">{{ $order['validity'] }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Section Title with lines -->
        <table class="section-header-table">
            <tr>
                <td><div class="section-header-line"></div></td>
                <td class="section-header-text">TAX INVOICE / RECEIPT</td>
                <td><div class="section-header-line"></div></td>
            </tr>
        </table>

        <!-- Plan Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 6%;">SR NO</th>
                    <th style="width: 38%; text-align: left; padding-left: 8px;">PRODUCT / PLAN DESCRIPTION</th>
                    <th style="width: 14%;">BILLING CYCLE</th>
                    <th style="width: 14%;">PRICE<br><span style="font-size: 7.5px; font-weight: normal; text-transform: none;">(Base Price)</span></th>
                    <th style="width: 14%;">RENEWAL AMOUNT<br><span style="font-size: 7.5px; font-weight: normal; text-transform: none;">(Renewal Rate)</span></th>
                    <th style="width: 14%;">TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; font-weight: bold;">1</td>
                    <td>
                        <div class="plan-title">{{ $product_title }} - {{ $plan->name }}</div>
                        <div class="plan-subtitle">Subscription: {{ $plan->subtitle ?? 'Complete Hospital Management System' }}</div>
                        <div class="plan-dates">
                            Start Date: {{ $plan->start_date ? \Carbon\Carbon::parse($plan->start_date)->format('Y-m-d') : '-' }} &nbsp;|&nbsp; 
                            End Date: {{ $plan->end_date ? \Carbon\Carbon::parse($plan->end_date)->format('Y-m-d') : '-' }}
                        </div>
                    </td>
                    <td style="text-align: center; text-transform: capitalize;">
                        {{ $plan->duration ?? '1 Year' }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        <span class="rupee">&#8377;</span>{{ number_format($plan->price ?? $plan->total_amount ?? 0, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        <span class="rupee">&#8377;</span>{{ number_format($plan->renewal_price ?? $plan->total_amount ?? $plan->price ?? 0, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 10.5px;">
                        <span class="rupee">&#8377;</span>{{ number_format($plan->total_amount ?? $plan->price ?? 0, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Total Bar -->
        <div class="total-container">
            <span class="total-label">TOTAL AMOUNT (INR):</span>
            <span class="total-box"><span class="rupee">&#8377;</span>{{ number_format($plan->total_amount ?? $plan->price ?? 0, 2) }}</span>
        </div>

        <!-- Featured Modules Card -->
        <div class="modules-card">
            <div class="modules-title">
                <span class="modules-title-icon">&#9638;</span> INCLUDED CLINIC / HMS MODULES
            </div>
            @php
                $chunks = array_chunk($featured_modules, 3);
            @endphp
            <table class="modules-table">
                @foreach($chunks as $row)
                    <tr>
                        @foreach($row as $col)
                            <td>
                                <span class="module-bullet">&bull;</span> {{ $col }}
                            </td>
                        @endforeach
                        @for($i = count($row); $i < 3; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>

        <!-- Terms and Signatory -->
        <table class="bottom-table">
            <tr>
                <td class="terms-box">
                    <div class="box-title">TERMS AND CONDITIONS</div>
                    <ol class="terms-ol">
                        @foreach($terms as $term)
                            <li>{{ $term }}</li>
                        @endforeach
                    </ol>
                </td>

                <td class="bottom-spacer"></td>

                <td class="sign-box">
                    <div class="box-title" style="text-align: left;">SIGNATORY</div>
                    <div class="sign-space">
                        @if(!empty($company['signature_url']))
                            <img src="{{ $company['signature_url'] }}" class="sign-img" alt="Signature">
                        @endif
                    </div>
                    <div class="sign-line"></div>
                    <div class="sign-title">FOR {{ $company['company_name'] }}</div>
                    <div class="sign-sub">Authorized Signatory</div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>
