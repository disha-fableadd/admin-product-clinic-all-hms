@extends('layout.app')

<style>
    :root {
        --theme-mint-soft: #cfece0;
        --theme-mint-dark: #26846b;
        --theme-coral-strip: #f89884;
        --theme-coral-dark: #e07b66;
        --card-bg: #ffffff;
        --border-color: #eaf3ef;
        --text-dark: #2c3e50;
        --text-muted: #7f8c8d;
    }

    /* Top Strip matching the uploaded Coral Header Reference */
    .report-top-banner {
        background: linear-gradient(90deg, #f89884 0%, #faaa99 100%);
        border-radius: 16px;
        padding: 22px 28px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(248, 152, 132, 0.25);
        color: #ffffff;
    }

    .report-title-wrap h3 {
        font-size: 23px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .report-title-wrap p {
        color: rgba(255, 255, 255, 0.92);
        font-size: 13.5px;
        margin: 0;
    }

    .report-filter-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-btn-group .btn {
        border-radius: 10px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transition: all 0.2s ease;
    }

    .filter-btn-group .btn:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #fff;
    }

    .filter-btn-group .btn.active {
        background: #ffffff !important;
        color: #e07b66 !important;
        border-color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-export-rep {
        background: #ffffff;
        color: #26846b !important;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 13.5px;
        font-weight: 700;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .btn-export-rep:hover {
        background: #cfece0;
        color: #1a624f !important;
        transform: translateY(-1px);
    }

    /* Metric Stat Card - Beautiful Mint & Coral Touch */
    .rep-stat-card {
        background: #ffffff;
        border: 1px solid #e7f5ef;
        border-radius: 16px;
        padding: 20px 22px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 14px rgba(38, 132, 107, 0.04);
        height: calc(100% - 22px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .rep-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(248, 152, 132, 0.12);
        border-color: #cfece0;
    }

    .rep-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .rep-stat-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
        margin: 0;
    }

    .rep-stat-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .icon-mint {
        color: #26846b;
        background: #cfece0;
    }

    .icon-coral {
        color: #e07b66;
        background: #fee9e4;
    }

    .rep-stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.2;
    }

    .rep-watermark-icon {
        position: absolute;
        right: -10px;
        bottom: -15px;
        font-size: 78px;
        color: rgba(38, 132, 107, 0.04);
        pointer-events: none;
    }

    /* Container Cards */
    .rep-box-card {
        background: #ffffff;
        border: 1px solid #edf6f2;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02);
    }

    .rep-box-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rep-box-header h4 {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .rep-box-header p {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }

    /* Revenue Summary Strip (Mint + Coral Accents) */
    .revenue-summary-strip {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
        background: #fbfdfc;
        border: 1px solid #cfece0;
        border-radius: 14px;
        padding: 16px 22px;
    }

    .rev-pill {
        flex: 1;
        min-width: 120px;
    }

    .rev-pill span {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
    }

    .rev-pill h5 {
        font-size: 19px;
        font-weight: 700;
        margin: 0;
    }

    .color-mint { color: #26846b !important; }
    .color-coral { color: #f89884 !important; }

    /* Status Mini Tiles */
    .status-summary-tile {
        background: #f8fbf9;
        border: 1px solid #e7f4ee;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
    }

    .status-tile-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .status-tile-info h5 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .status-tile-info p {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
    }

    /* Progress bar */
    .rep-progress {
        height: 9px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        margin-top: 6px;
    }

    .rep-progress-bar-mint {
        background: linear-gradient(90deg, #26846b, #48b899);
        height: 100%;
        border-radius: 10px;
    }

    .rep-progress-bar-coral {
        background: linear-gradient(90deg, #f89884, #faaa99);
        height: 100%;
        border-radius: 10px;
    }

    /* Tables with Theme Headers */
    .rep-table-wrap {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e6f3ed;
    }

    .rep-table {
        margin: 0;
        width: 100%;
        border-collapse: separate;
    }

    .rep-table thead th {
        background: #cfece0;
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 18px;
        border: none;
    }

    .rep-table tbody td {
        padding: 13px 18px;
        font-size: 13.5px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .rep-table tbody tr:hover {
        background-color: #f7fcf9;
    }

    .badge-pill-custom {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-paid {
        background: #cfece0;
        color: #1a624f;
    }

    .badge-pending {
        background: #fee9e4;
        color: #d9534f;
    }

    .badge-failed {
        background: #f1f5f9;
        color: #64748b;
    }

    .view-all-link {
        font-size: 13.5px;
        font-weight: 600;
        color: #26846b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .view-all-link:hover {
        color: #f89884;
        text-decoration: underline;
    }

    .rep-table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rep-pagination-wrap {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .rep-page-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .rep-page-btn:hover:not(:disabled) {
        border-color: #26846b;
        color: #26846b;
        background: #cfece0;
    }

    .rep-page-btn.active {
        background: #26846b !important;
        border-color: #26846b !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(38, 132, 107, 0.25);
    }

    .rep-page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    @media print {
        .header, .sidebar, .sidebar-overlay, .mobile-bottom-nav, .report-filter-wrap, .view-all-link {
            display: none !important;
        }
        .page-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .content {
            padding: 0 !important;
        }
        .report-top-banner {
            box-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .rep-stat-card, .rep-box-card {
            box-shadow: none !important;
            border: 1px solid #dcdcdc !important;
            page-break-inside: avoid;
        }
    }
</style>

@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Top Header with Coral & Mint Color Combination -->
        <div class="report-top-banner">
            <div class="report-title-wrap">
                <h3><i class="fas fa-chart-line"></i> Clinic Reports & Analytics</h3>
                <p>Monitor patient registrations, doctor performance, appointment flow, and clinic revenue in real time.</p>
                <div id="activePeriodLabel" style="font-size: 13px; margin-top: 6px; color: rgba(255,255,255,0.95); font-weight: 600;">
                    <i class="fas fa-calendar-alt mr-1"></i> Monthly Report
                </div>
            </div>
            <div class="report-filter-wrap">
                <div class="btn-group filter-btn-group" role="group">
                    <button type="button" class="btn period-filter-btn" data-filter="today">Today</button>
                    <button type="button" class="btn period-filter-btn" data-filter="week">Weekly</button>
                    <button type="button" class="btn period-filter-btn active" data-filter="month">Monthly</button>
                    <button type="button" class="btn period-filter-btn" data-filter="year">Yearly</button>
                </div>
               
            </div>
        </div>

        <!-- 8 Stat Metric Boxes with Mint & Coral Palette -->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Total Patients</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-user-injured"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalPatients">0</h3>
                    <i class="fas fa-user-injured rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Doctors & Staff</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-user-md"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalDoctors">0</h3>
                    <i class="fas fa-user-md rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Appointments (Period)</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardPeriodAppointments">0</h3>
                    <i class="fas fa-calendar-check rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Follow-ups Scheduled</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-clipboard-check"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardPeriodFollowups">0</h3>
                    <i class="fas fa-clipboard-check rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Active Treatments</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-stethoscope"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalTreatments">0</h3>
                    <i class="fas fa-stethoscope rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Reference Doctors</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-hospital-user"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalRefDoctors">0</h3>
                    <i class="fas fa-hospital-user rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Total Revenue</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-rupee-sign"></i></div>
                    </div>
                    <h3 class="rep-stat-number color-mint" id="cardTotalRevenue">₹ 0</h3>
                    <i class="fas fa-rupee-sign rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Invoices / Billings</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <h3 class="rep-stat-number color-coral" id="cardTotalTransactions">0</h3>
                    <i class="fas fa-file-invoice-dollar rep-watermark-icon"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Overview Chart Section (Coral & Mint accents) -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Revenue Overview</h4>
                            <p>Track your billing trends and clinic transaction performance</p>
                        </div>
                    </div>

                    <div class="revenue-summary-strip">
                        <div class="rev-pill">
                            <span>Total Revenue</span>
                            <h5 class="text-dark" id="stripTotalRevenue">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Paid Revenue</span>
                            <h5 class="color-mint" id="stripPaidInvoices">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Pending Revenue</span>
                            <h5 class="color-coral" id="stripPendingInvoices">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Paid Invoices Count</span>
                            <h5 class="text-secondary" id="stripCompletedCount">0</h5>
                        </div>
                    </div>

                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown Doughnut + Clinic Progress Bars -->
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="rep-box-card" style="height: calc(100% - 25px);">
                    <div class="rep-box-header">
                        <div>
                            <h4>Appointment Status Distribution</h4>
                            <p>Overview of appointment progress in selected period</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-center">
                            <div style="max-height: 220px; position: relative;">
                                <canvas id="statusDoughnutChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#cfece0; color:#26846b;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countCompletedApp">0</h5>
                                    <p>Completed Appointments</p>
                                </div>
                            </div>

                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#fee9e4; color:#e07b66;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countUpcomingApp">0</h5>
                                    <p>Upcoming / Confirmed</p>
                                </div>
                            </div>

                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#f1f5f9; color:#64748b;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countCancelledApp">0</h5>
                                    <p>Cancelled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="rep-box-card" style="height: calc(100% - 25px);">
                    <div class="rep-box-header">
                        <div>
                            <h4>Clinic Activity Analysis</h4>
                            <p>Summary of patient care and treatment volume</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="status-summary-tile" style="margin: 0;">
                                <div class="status-tile-icon" style="background:#cfece0; color:#26846b;">
                                    <i class="fas fa-user-injured"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="tilePatientsCount">0</h5>
                                    <p>Registered Patients</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="status-summary-tile" style="margin: 0;">
                                <div class="status-tile-icon" style="background:#fee9e4; color:#e07b66;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="tileFollowupsCount">0</h5>
                                    <p>Follow-up Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13.5px; font-weight: 600; color: #475569;">Appointment Completion Rate</span>
                            <span style="font-size: 13.5px; font-weight: 700; color: #26846b;" id="completionRateLabel">0%</span>
                        </div>
                        <div class="rep-progress">
                            <div class="rep-progress-bar-mint" id="completionProgressBar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13.5px; font-weight: 600; color: #475569;">Payment Collection Rate</span>
                            <span style="font-size: 13.5px; font-weight: 700; color: #e07b66;" id="collectionRateLabel">0%</span>
                        </div>
                        <div class="rep-progress">
                            <div class="rep-progress-bar-coral" id="collectionProgressBar" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table 1: Treatment Performance Report -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Treatment Performance Report</h4>
                            <p>Breakdown of clinic treatments, assigned doctors, and appointment counts</p>
                        </div>
                        <a href="{{ route('treatment.index') }}" class="view-all-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="rep-table-wrap table-responsive">
                        <table class="table rep-table">
                            <thead>
                                <tr>
                                    <th>Treatment Name</th>
                                    <th>Doctor In-Charge</th>
                                    <th>Standard Fee</th>
                                    <th>Appointments Booked</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="treatmentReportTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Loading treatment data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="rep-table-footer">
                        <div id="treatmentPaginationInfo" class="text-muted" style="font-size: 13px; font-weight: 500;"></div>
                        <div id="treatmentPaginationControls" class="rep-pagination-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table 2: Recent Transactions / Invoices Report -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Recent Transaction Report</h4>
                            <p>Filterable view of patient billings and payment statuses</p>
                        </div>
                        <a href="{{ route('invoice.index') }}" class="view-all-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="rep-table-wrap table-responsive">
                        <table class="table rep-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Patient Name</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactionReportTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Loading transaction data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="rep-table-footer">
                        <div id="transactionPaginationInfo" class="text-muted" style="font-size: 13px; font-weight: 500;"></div>
                        <div id="transactionPaginationControls" class="rep-pagination-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let revenueChartInstance = null;
    let statusDoughnutChartInstance = null;
    let activeFilter = 'month';

    let allTreatments = [];
    let treatmentCurrentPage = 1;
    const treatmentPageSize = 5;

    let allTransactions = [];
    let transactionCurrentPage = 1;
    const transactionPageSize = 5;

    function getSelectedBranchId() {
        return localStorage.getItem('selectedBranchId') || '';
    }

    function formatCurrency(amount) {
        let val = Number(amount) || 0;
        return '₹ ' + val.toLocaleString('en-IN');
    }

    function loadReportData(filter = 'month') {
        const token = localStorage.getItem('token') || '';
        const branchId = getSelectedBranchId();

        let headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }

        $.ajax({
            url: "{{ url('/api/report-overview') }}",
            type: "GET",
            dataType: "json",
            headers: headers,
            data: {
                filter: filter,
                branch_id: branchId
            },
            beforeSend: function() {
                $('#treatmentReportTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Loading treatment performance...</td></tr>');
                $('#transactionReportTableBody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Loading transactions...</td></tr>');
                $('#treatmentPaginationInfo').empty();
                $('#treatmentPaginationControls').empty();
                $('#transactionPaginationInfo').empty();
                $('#transactionPaginationControls').empty();
            },
            success: function(res) {
                if (res && res.period_label) {
                    $('#activePeriodLabel').html('<i class="fas fa-calendar-alt mr-1"></i> ' + res.period_label);
                }
                renderMetrics(res?.metrics);
                renderRevenueTrend(res?.revenue_chart);
                renderStatusDoughnut(res?.appointment_distribution);

                allTreatments = res?.treatment_performance || [];
                treatmentCurrentPage = 1;
                renderTreatmentPage();

                allTransactions = res?.recent_transactions || [];
                transactionCurrentPage = 1;
                renderTransactionPage();
            },
            error: function(xhr, status, error) {
                console.error("Error loading report data:", xhr.responseText || error);
                $('#treatmentReportTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle mr-2"></i> Unable to load treatment records.</td></tr>');
                $('#transactionReportTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle mr-2"></i> Unable to load transaction records.</td></tr>');
                $('#treatmentPaginationInfo').empty();
                $('#treatmentPaginationControls').empty();
                $('#transactionPaginationInfo').empty();
                $('#transactionPaginationControls').empty();
            }
        });
    }

    function renderMetrics(m) {
        if (!m) return;

        $('#cardTotalPatients').text(Number(m.total_patients || 0).toLocaleString('en-IN'));
        $('#cardTotalDoctors').text(Number(m.total_doctors || 0).toLocaleString('en-IN'));
        $('#cardPeriodAppointments').text(Number(m.total_appointments || 0).toLocaleString('en-IN'));
        $('#cardPeriodFollowups').text(Number(m.total_followups || 0).toLocaleString('en-IN'));
        $('#cardTotalTreatments').text(Number(m.total_treatments || 0).toLocaleString('en-IN'));
        $('#cardTotalRefDoctors').text(Number(m.total_reference_doctors || 0).toLocaleString('en-IN'));
        $('#cardTotalRevenue').text(formatCurrency(m.total_revenue));
        $('#cardTotalTransactions').text(Number(m.total_transactions || 0).toLocaleString('en-IN'));

        $('#stripTotalRevenue').text(formatCurrency(m.total_revenue));
        $('#stripPaidInvoices').text(formatCurrency(m.paid_revenue));
        $('#stripPendingInvoices').text(formatCurrency(m.pending_revenue));
        $('#stripCompletedCount').text(Number(m.paid_transactions || 0).toLocaleString('en-IN'));

        $('#countCompletedApp').text(Number(m.completed_appointments || 0).toLocaleString('en-IN'));
        $('#countUpcomingApp').text(Number(m.upcoming_appointments || 0).toLocaleString('en-IN'));
        $('#countCancelledApp').text(Number(m.cancelled_appointments || 0).toLocaleString('en-IN'));

        $('#tilePatientsCount').text(Number(m.total_patients || 0).toLocaleString('en-IN'));
        $('#tileFollowupsCount').text(Number(m.total_followups || 0).toLocaleString('en-IN'));

        // Completion Rate
        let totalApps = Number(m.total_appointments) || 0;
        let completedApps = Number(m.completed_appointments) || 0;
        let compRate = totalApps > 0 ? Math.round((completedApps / totalApps) * 100) : 0;
        $('#completionRateLabel').text(compRate + '%');
        $('#completionProgressBar').css('width', Math.min(100, Math.max(0, compRate)) + '%');

        // Payment Collection Rate
        let totalRev = Number(m.total_revenue) || 0;
        let paidRev = Number(m.paid_revenue) || 0;
        let collRate = totalRev > 0 ? Math.round((paidRev / totalRev) * 100) : 0;
        $('#collectionRateLabel').text(collRate + '%');
        $('#collectionProgressBar').css('width', Math.min(100, Math.max(0, collRate)) + '%');
    }

    function renderRevenueTrend(chartData) {
        const canvas = document.getElementById('revenueTrendChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (revenueChartInstance) {
            revenueChartInstance.destroy();
        }

        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(248, 152, 132, 0.45)');
        gradient.addColorStop(0.6, 'rgba(207, 236, 224, 0.25)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0.0)');

        const isV2 = (window.Chart && window.Chart.version && String(window.Chart.version).startsWith('2'));

        let chartConfig = {
            type: 'line',
            data: {
                labels: chartData?.labels || [],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: chartData?.values || [],
                    borderColor: '#f89884',
                    backgroundColor: gradient,
                    borderWidth: 2.8,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#26846b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Revenue: ₹ ' + Number(context.parsed.y).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(item) {
                            let val = item.yLabel !== undefined ? item.yLabel : item.value;
                            return ' Revenue: ₹ ' + Number(val).toLocaleString('en-IN');
                        }
                    }
                },
                scales: isV2 ? {
                    xAxes: [{ gridLines: { display: false } }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(val) { return '₹' + Number(val).toLocaleString('en-IN'); }
                        },
                        gridLines: { color: '#f1f5f9' }
                    }]
                } : {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(val) { return '₹' + Number(val).toLocaleString('en-IN'); }
                        }
                    }
                }
            }
        };

        revenueChartInstance = new Chart(ctx, chartConfig);
    }

    function renderStatusDoughnut(dist) {
        const canvas = document.getElementById('statusDoughnutChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (statusDoughnutChartInstance) {
            statusDoughnutChartInstance.destroy();
        }

        let completed = dist?.completed || 0;
        let upcoming = dist?.upcoming || 0;
        let cancelled = dist?.cancelled || 0;

        let chartData = [completed, upcoming, cancelled];
        let labels = ['Completed', 'Upcoming / Confirmed', 'Cancelled'];
        let bgColors = ['#26846b', '#f89884', '#cbd5e1'];

        if (completed === 0 && upcoming === 0 && cancelled === 0) {
            chartData = [1];
            labels = ['No appointments yet'];
            bgColors = ['#cfece0'];
        }

        statusDoughnutChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: chartData,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                cutoutPercentage: 72,
                legend: { display: false },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    function buildPaginationButtons(currentPage, totalPages, onClickFnName) {
        if (totalPages <= 1) {
            return '';
        }

        let html = '';
        html += `<button type="button" class="rep-page-btn" ${currentPage <= 1 ? 'disabled' : ''} onclick="${onClickFnName}(${currentPage - 1})" title="Previous Page"><i class="fas fa-chevron-left"></i></button>`;

        for (let p = 1; p <= totalPages; p++) {
            if (totalPages > 7) {
                if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
                    html += `<button type="button" class="rep-page-btn ${p === currentPage ? 'active' : ''}" onclick="${onClickFnName}(${p})">${p}</button>`;
                } else if (p === currentPage - 2 || p === currentPage + 2) {
                    html += `<span class="px-1 text-muted">...</span>`;
                }
            } else {
                html += `<button type="button" class="rep-page-btn ${p === currentPage ? 'active' : ''}" onclick="${onClickFnName}(${p})">${p}</button>`;
            }
        }

        html += `<button type="button" class="rep-page-btn" ${currentPage >= totalPages ? 'disabled' : ''} onclick="${onClickFnName}(${currentPage + 1})" title="Next Page"><i class="fas fa-chevron-right"></i></button>`;

        return html;
    }

    function renderTreatmentPage() {
        const tbody = $('#treatmentReportTableBody');
        const info = $('#treatmentPaginationInfo');
        const controls = $('#treatmentPaginationControls');
        tbody.empty();

        const total = allTreatments.length;
        if (total === 0) {
            tbody.append('<tr><td colspan="5" class="text-center py-4 text-muted">No treatments recorded.</td></tr>');
            info.text('Showing 0 to 0 of 0 entries');
            controls.empty();
            return;
        }

        const totalPages = Math.ceil(total / treatmentPageSize);
        if (treatmentCurrentPage > totalPages) treatmentCurrentPage = totalPages;
        if (treatmentCurrentPage < 1) treatmentCurrentPage = 1;

        const startIdx = (treatmentCurrentPage - 1) * treatmentPageSize;
        const endIdx = Math.min(startIdx + treatmentPageSize, total);
        const pageItems = allTreatments.slice(startIdx, endIdx);

        pageItems.forEach(function(item) {
            let doc = item.doctor_name || 'General Staff';
            let price = item.price ? ('₹ ' + Number(item.price).toLocaleString('en-IN')) : 'Free / Not set';
            let count = item.total_appointments || 0;

            tbody.append(`
                <tr>
                    <td style="font-weight: 600; color: #1e293b;">${item.name || '--'}</td>
                    <td><i class="fas fa-user-md mr-1" style="color: #26846b;"></i> ${doc}</td>
                    <td style="font-weight: 600;">${price}</td>
                    <td><span class="badge" style="background:#cfece0; color:#26846b; font-size: 13px; font-weight: 700; padding: 6px 12px; border-radius: 8px;">${count}</span></td>
                    <td><span class="badge-pill-custom badge-paid">Active</span></td>
                </tr>
            `);
        });

        info.text(`Showing ${startIdx + 1} to ${endIdx} of ${total} entries`);
        controls.html(buildPaginationButtons(treatmentCurrentPage, totalPages, 'goToTreatmentPage'));
    }

    window.goToTreatmentPage = function(page) {
        const totalPages = Math.ceil(allTreatments.length / treatmentPageSize);
        if (page < 1 || page > totalPages) return;
        treatmentCurrentPage = page;
        renderTreatmentPage();
    };

    function renderTreatmentTable(treatments) {
        allTreatments = treatments || [];
        treatmentCurrentPage = 1;
        renderTreatmentPage();
    }

    function renderTransactionPage() {
        const tbody = $('#transactionReportTableBody');
        const info = $('#transactionPaginationInfo');
        const controls = $('#transactionPaginationControls');
        tbody.empty();

        const total = allTransactions.length;
        if (total === 0) {
            tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">No transactions found for this period.</td></tr>');
            info.text('Showing 0 to 0 of 0 entries');
            controls.empty();
            return;
        }

        const totalPages = Math.ceil(total / transactionPageSize);
        if (transactionCurrentPage > totalPages) transactionCurrentPage = totalPages;
        if (transactionCurrentPage < 1) transactionCurrentPage = 1;

        const startIdx = (transactionCurrentPage - 1) * transactionPageSize;
        const endIdx = Math.min(startIdx + transactionPageSize, total);
        const pageItems = allTransactions.slice(startIdx, endIdx);

        pageItems.forEach(function(item) {
            let status = (item.status || 'Pending').toLowerCase();
            let badgeClass = 'badge-pending';
            if (status === 'paid' || status === 'completed') badgeClass = 'badge-paid';
            else if (status === 'failed' || status === 'cancelled') badgeClass = 'badge-failed';

            let date = item.date || '--';
            let amount = formatCurrency(item.amount);
            let method = item.method || 'Cash / Offline';

            tbody.append(`
                <tr>
                    <td style="font-weight: 700; color: #26846b;">#INV-${item.id}</td>
                    <td style="font-weight: 600;">${item.patient_name || '--'}</td>
                    <td><span class="text-capitalize">${item.type || 'General'}</span></td>
                    <td>${date}</td>
                    <td style="font-weight: 700; color: #1e293b;">${amount}</td>
                    <td>${method}</td>
                    <td><span class="badge-pill-custom ${badgeClass}">${item.status || 'Pending'}</span></td>
                </tr>
            `);
        });

        info.text(`Showing ${startIdx + 1} to ${endIdx} of ${total} entries`);
        controls.html(buildPaginationButtons(transactionCurrentPage, totalPages, 'goToTransactionPage'));
    }

    window.goToTransactionPage = function(page) {
        const totalPages = Math.ceil(allTransactions.length / transactionPageSize);
        if (page < 1 || page > totalPages) return;
        transactionCurrentPage = page;
        renderTransactionPage();
    };

    function renderTransactionTable(transactions) {
        allTransactions = transactions || [];
        transactionCurrentPage = 1;
        renderTransactionPage();
    }

    $(document).ready(function() {
        loadReportData(activeFilter);

        $('.period-filter-btn').on('click', function() {
            $('.period-filter-btn').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            loadReportData(activeFilter);
        });

        $('#btnPrintReport').on('click', function() {
            window.print();
        });

        // Listen for branch changes across tabs or from branch selectors
        window.addEventListener('storage', function(e) {
            if (e.key === 'selectedBranchId') {
                loadReportData(activeFilter);
            }
        });
    });
</script>
@endsection