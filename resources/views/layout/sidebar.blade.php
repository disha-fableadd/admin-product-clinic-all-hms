@php
    use App\Models\Setting;
    $currentProjectTypeId = (int) Setting::getValue('project_type_id', 1);

    // Active state checkers
    $isDashboardActive = request()->routeIs('dashboard');

    $isClinicalActive = request()->routeIs(
        'opd_visit.*', 'ipd_admit.*', 'daily_data.*',
        'therapy.*', 'assign-therapy.*',
        'dietchart.*',
        'assessment.*', 'homeadvice.*', 'soap.*',
        'pathology.*', 'pathology_reports.*', 'radiology-tests.*', 'radiology-reports.*', 'service.*',
        'ot.*', 'discharge.*',
        'report.*'
    );

    $isAccountingActive = request()->routeIs(
        'account_ledger.*', 'inventory.*', 'expense.*', 'invoice.*'
    );

    $isReportsActive = request()->routeIs(
        'chart', 'sales.report', 'purchase.report', 'expense.report') || request()->is('chart*', 'sales-report*', 'purchase-report*', 'expense-report*'
    );

    $isSettingsActive = request()->routeIs(
        'setting.*', 'settings.*', 'taxrate.*', 'plans.*', 'email.*', 'sms.*', 'calender.*', 'knowledge.*', 'profile'
    );

    // Default first menu (HMS Modules / ERP) to active and open on dashboard or refresh when no other category is active
    $isErpActive = request()->routeIs(
        'patients.*', 'diagnosis.*', 'referaldoctor.*',
        'appointment.*', 'followup.*',
        'treatment.*', 'treatment_booking.*', 'machine.*',
        'medicine.*', 'category.*', 'brand.*', 'medicine_unit.*', 'symptoms.*',
        'sales.*', 'purchase.*',
        'supplier.*', 'vendor.*',
        'user.*'
    ) || $isDashboardActive || (!$isClinicalActive && !$isAccountingActive && !$isReportsActive && !$isSettingsActive);

    $showErpSection = tenant_has_module(['Patients', 'Appointments', 'Medicines', 'Followup', 'Staffs', 'Sale Bill', 'Purchase Bill', 'Vendors', 'Treatments']);
    $showClinicalSection = tenant_has_module(['OPD', 'IPD', 'Daily Register', 'Therapy', 'Diet Chart', 'Clinical Notes', 'Diagnostic Service', 'Medical Report', 'OT & Discharge', 'Clinical Services']);
    $showAccountingSection = tenant_has_module(['Account Ledger', 'Stock / Inventory', 'Expenses', 'Invoices', 'Accounting']);
    $showReportsSection = tenant_has_module(['Sales Report', 'Purchase Report', 'Expense Report', 'Analytics & Summary', 'Reports', 'Report']);
    $showSettingsSection = tenant_has_module(['Settings', 'Tax Rates', 'My Plan Details', 'Calendar', 'Knowledge Base']) || true;

    $hasPatientPerm = tenant_has_module('Patients') && (app('hasPermission')(5, 'view') || app('hasPermission')(5, 'create') || app('hasPermission')(35, 'view') || app('hasPermission')(36, 'view'));
@endphp

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll" style="padding: 0 !important;">
        <div id="sidebar-menu" class="sidebar-menu" style="padding: 6px 8px !important; margin: 0 !important;">
            <style>
                .sidebar .sidebar-inner {
                    padding: 0 !important;
                }
                .sidebar #sidebar-menu {
                    padding: 6px 8px !important;
                    margin: 0 !important;
                }
                .sidebar #sidebar-menu ul {
                    padding: 0 !important;
                    margin: 0 !important;
                    list-style: none !important;
                }
                .sidebar #sidebar-menu ul li {
                    list-style: none !important;
                    list-style-type: none !important;
                }

                /* Sidebar Search */
                #sidebar-menu .sidebar-search-wrap {
                    display: flex;
                    align-items: center;
                    box-sizing: border-box;
                    width: 100%;
                    height: 38px;
                    margin: 2px 0 10px 0 !important;
                    padding: 0 12px;
                    background: #ffffff;
                    border: 1px solid #d1d5db;
                    border-radius: 8px;
                    box-shadow: none;
                }
                #sidebar-menu .sidebar-search-wrap:focus-within {
                    border-color: #f89884;
                    box-shadow: 0 0 0 2px rgba(248, 152, 132, 0.2);
                }
                #sidebar-menu .sidebar-search-icon {
                    flex-shrink: 0;
                    margin-right: 8px;
                    color: #9ca3af;
                    font-size: 13px;
                }
                #sidebar-menu #sidebar-module-search {
                    flex: 1;
                    width: 100%;
                    min-width: 0;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    border: none !important;
                    outline: none !important;
                    background: transparent !important;
                    font-size: 13.5px;
                    font-weight: 400;
                    color: #374151;
                }
                #sidebar-menu #sidebar-module-search::placeholder {
                    color: #9ca3af;
                }

                /* Category Headers (Dashboard, ERP, Clinical, Accounting, Reports, Settings) */
                .sidebar .sidebar-menu > ul > li.sidebar-category {
                    margin-bottom: 7px !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category > a {
                    border-radius: 10px !important;
                    font-weight: 700 !important;
                    font-size: 14.5px !important;
                    padding: 12px 34px 12px 12px !important;
                    display: flex !important;
                    align-items: center !important;
                    position: relative !important;
                    border-left: 3.5px solid transparent !important;
                    transition: all 0.2s ease;
                    color: #092c4c !important;
                } 
                .sidebar .sidebar-menu > ul > li.sidebar-category > a > i {
                    width: 20px;
                    text-align: center;
                    font-size: 15px;
                    margin-right: 8px !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category > a > span:not(.menu-arrow) {
                    flex: 1 1 auto !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }

                /* Category Tint Colors */
                .sidebar .sidebar-menu > ul > li.sidebar-category-dashboard > a {
                    background: rgba(220, 53, 69, 0.08) !important;
                    border-left-color: #dc3545 !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category-erp > a {
                    background: rgba(13, 110, 253, 0.08) !important;
                    border-left-color: #0d6efd !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category-clinical > a {
                    background: rgba(245, 158, 11, 0.08) !important;
                    border-left-color: #f59e0b !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category-accounting > a {
                    background: rgba(25, 135, 84, 0.08) !important;
                    border-left-color: #198754 !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category-reports > a {
                    background: rgba(111, 66, 193, 0.08) !important;
                    border-left-color: #6f42c1 !important;
                }
                .sidebar .sidebar-menu > ul > li.sidebar-category-settings > a {
                    background: rgba(13, 202, 240, 0.08) !important;
                    border-left-color: #0dcaf0 !important;
                }

                /* Arrows */
                .sidebar .sidebar-menu li a .menu-arrow {
                    position: absolute !important;
                    right: 12px !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    line-height: 1 !important;
                    font-size: 13px !important;
                    width: 14px !important;
                    text-align: center !important;
                    margin: 0 !important;
                    transition: transform 0.2s ease !important;
                    color: inherit !important;
                }
                .sidebar .sidebar-menu li a.subdrop .menu-arrow,
                .sidebar .sidebar-menu li.submenu.active > a > .menu-arrow {
                    transform: translateY(-50%) rotate(90deg) !important;
                }

                /* Module Container inside Categories */
                body #sidebar-menu ul > li.sidebar-category > ul {
                    padding: 2px 0 4px 0 !important;
                    margin: 0 !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module {
                    margin: 2px 0 !important;
                    padding: 0 !important;
                    list-style: none !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module::marker {
                    display: none !important;
                    content: none !important;
                }

                /* INACTIVE MODULE STYLE (Matching Image 3) */
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > a,
                body #sidebar-menu ul > li.sidebar-category > ul > li > a {
                    display: flex !important;
                    align-items: center !important;
                    position: relative !important;
                    border-radius: 12px !important;
                    padding: 8px 34px 8px 10px !important;
                    font-size: 14px !important;
                    font-weight: 500 !important;
                    color: #555555 !important;
                    transition: all 0.15s ease;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > a > i.icons,
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > a > i:not(.menu-arrow),
                body #sidebar-menu ul > li.sidebar-category > ul > li > a > i.icons,
                body #sidebar-menu ul > li.sidebar-category > ul > li > a > i:not(.menu-arrow) {
                    display: inline-block !important;
                    width: 22px !important;
                    text-align: center;
                    font-size: 15px;
                    margin-right: 8px !important;
                    color: #78889b !important;
                    transition: color 0.15s ease;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > a > span:not(.menu-arrow),
                body #sidebar-menu ul > li.sidebar-category > ul > li > a > span:not(.menu-arrow) {
                    flex: 1 1 auto !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > a .menu-arrow,
                body #sidebar-menu ul > li.sidebar-category > ul > li > a .menu-arrow {
                    color: #78889b !important;
                }

                /* ACTIVE MODULE STYLE (Matching Image 2 & 3)
                   Solid #cfece0 pill, pure black text, pure black icon & arrow */
                .sidebar .sidebar-menu li.sidebar-module.active > a,
                .sidebar .sidebar-menu li.sidebar-module > a.active,
                .sidebar .sidebar-menu li.sidebar-module > a.subdrop,
                .sidebar .sidebar-menu li.sidebar-category > ul > li.active > a,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.active,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.subdrop {
                    background-color: #cfece0 !important;
                    color: #000000 !important;
                    font-weight: 600 !important;
                    border-radius: 12px !important;
                }
                .sidebar .sidebar-menu li.sidebar-module.active > a i,
                .sidebar .sidebar-menu li.sidebar-module > a.active i,
                .sidebar .sidebar-menu li.sidebar-module > a.subdrop i,
                .sidebar .sidebar-menu li.sidebar-category > ul > li.active > a i,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.active i,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.subdrop i,
                .sidebar .sidebar-menu li.sidebar-module.active > a span,
                .sidebar .sidebar-menu li.sidebar-module > a.active span,
                .sidebar .sidebar-menu li.sidebar-module > a.subdrop span,
                .sidebar .sidebar-menu li.sidebar-category > ul > li.active > a span,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.active span,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.subdrop span,
                .sidebar .sidebar-menu li.sidebar-module.active > a .menu-arrow,
                .sidebar .sidebar-menu li.sidebar-module > a.active .menu-arrow,
                .sidebar .sidebar-menu li.sidebar-module > a.subdrop .menu-arrow,
                .sidebar .sidebar-menu li.sidebar-category > ul > li.active > a .menu-arrow,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.active .menu-arrow,
                .sidebar .sidebar-menu li.sidebar-category > ul > li > a.subdrop .menu-arrow {
                    color: #000000 !important;
                }

                /* Sub-items (Child links like All Staffs / Add Staff) */
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul {
                    padding: 3px 0 !important;
                    margin: 0 !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li {
                    margin: 2px 0 !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a {
                    display: flex !important;
                    align-items: center !important;
                    padding: 7px 12px 7px 22px !important;
                    font-size: 13.5px !important;
                    font-weight: 500 !important;
                    color: #000000 !important;
                    border-radius: 12px !important;
                    margin: 1px 0 !important;
                    transition: all 0.15s ease;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a i.icons,
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a i:not(.menu-arrow) {
                    display: inline-block !important;
                    width: 20px !important;
                    text-align: center;
                    font-size: 14px;
                    margin-right: 8px !important;
                    color: #000000 !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a > span {
                    padding-left: 2px !important;
                }

                /* ACTIVE CHILD LINK (e.g. All Staffs in Image 2 & 3) */
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li.active > a,
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a.active {
                    background-color: #cfece0 !important;
                    color: #000000 !important;
                    font-weight: 600 !important;
                    border-radius: 12px !important;
                }
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li.active > a i,
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a.active i {
                    color: #000000 !important;
                }

                /* Inactive Child Hover */
                body #sidebar-menu ul > li.sidebar-category > ul > li.sidebar-module > ul > li > a:hover:not(.active) {
                    background-color: rgba(207, 236, 224, 0.4) !important;
                    color: #000000 !important;
                }
            </style>
            
            <!-- Real-time Module Search -->
            <div class="sidebar-search-wrap">
                <i class="fas fa-search sidebar-search-icon"></i>
                <input type="text" id="sidebar-module-search" class="sidebar-search-input"
                    placeholder="Search..." autocomplete="off">
            </div>

            <ul>
                {{-- 1. DASHBOARD --}}
                @if (tenant_has_module('Dashboard'))
                <li class="sidebar-category sidebar-category-dashboard {{ $isDashboardActive ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="{{ $isDashboardActive ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt text-danger"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endif

                {{-- 2. ERP (Core Business Modules) --}}

                @if ($showErpSection)
                <li class="submenu sidebar-category sidebar-category-erp {{ $isErpActive ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="{{ $isErpActive ? 'subdrop active' : '' }}">
                        <i class="fas fa-industry text-primary"></i>
                        <span>HMS Modules</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ $isErpActive ? 'display: block;' : 'display: none;' }}">

                        {{-- Patients --}}
                        @if ($hasPatientPerm)
                            <li class="submenu sidebar-module {{ request()->routeIs('patients.*', 'diagnosis.*', 'referaldoctor.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('patients.*', 'diagnosis.*', 'referaldoctor.*') ? 'subdrop active' : '' }}">
                                    <i class="fas fa-wheelchair icons"></i>
                                    <span>Patients</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('patients.*', 'diagnosis.*', 'referaldoctor.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (app('hasPermission')(5, 'view'))
                                        <li class="{{ request()->routeIs('patients.index') ? 'active' : '' }}">
                                            <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.index') ? 'active' : '' }}">
                                                <i class="fa fa-procedures icons"></i>
                                                <span>All Patients</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(5, 'create'))
                                        <li class="{{ request()->routeIs('patients.create') ? 'active' : '' }}">
                                            <a href="{{ route('patients.create') }}" class="{{ request()->routeIs('patients.create') ? 'active' : '' }}">
                                                <i class="fa fa-user-injured icons"></i>
                                                <span>Add Patient</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(35, 'view'))
                                        <li class="{{ request()->routeIs('diagnosis.index') ? 'active' : '' }}">
                                            <a href="{{ route('diagnosis.index') }}" class="{{ request()->routeIs('diagnosis.index') ? 'active' : '' }}">
                                                <i class="fa fa-stethoscope icons"></i>
                                                <span>Diagnosis</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(36, 'view'))
                                        <li class="{{ request()->routeIs('referaldoctor.index') ? 'active' : '' }}">
                                            <a href="{{ route('referaldoctor.index') }}" class="{{ request()->routeIs('referaldoctor.index') ? 'active' : '' }}">
                                                <i class="fa fa-user-md icons"></i>
                                                <span>Reference Doctors</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Appointments --}}
                        @if (tenant_has_module('Appointments') && app('hasPermission')(6, 'view'))
                            <li class="submenu sidebar-module {{ request()->routeIs('appointment.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('appointment.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-calendar-check icons"></i>
                                    <span>Appointments</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('appointment.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (tenant_has_module('Appointments') && app('hasPermission')(6, 'view'))
                                        <li class="{{ request()->routeIs('appointment.index') ? 'active' : '' }}">
                                            <a href="{{ route('appointment.index') }}" class="{{ request()->routeIs('appointment.index') ? 'active' : '' }}">
                                                <i class="fa fa-calendar-alt icons"></i>
                                                <span>All Appointments</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(6, 'create'))
                                        <li class="{{ request()->routeIs('appointment.create') ? 'active' : '' }}">
                                            <a href="{{ route('appointment.create') }}" class="{{ request()->routeIs('appointment.create') ? 'active' : '' }}">
                                                <i class="fa fa-calendar-plus icons"></i>
                                                <span>Add Appointment</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                         {{-- Medicines --}}
                        @if (tenant_has_module('Medicines') && (app('hasPermission')(1, 'view') || app('hasPermission')(4, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('medicine.*', 'category.*', 'brand.*', 'medicine_unit.*', 'symptoms.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('medicine.*', 'category.*', 'brand.*', 'medicine_unit.*', 'symptoms.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-pills icons"></i>
                                    <span>Medicines</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('medicine.*', 'category.*', 'brand.*', 'medicine_unit.*', 'symptoms.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('medicine.index') ? 'active' : '' }}">
                                        <a href="{{ route('medicine.index') }}" class="{{ request()->routeIs('medicine.index') ? 'active' : '' }}">
                                            <i class="fa fa-pills icons"></i>
                                            <span>All Medicines</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(1, 'create'))
                                        <li class="{{ request()->routeIs('medicine.create') ? 'active' : '' }}">
                                            <a href="{{ route('medicine.create') }}" class="{{ request()->routeIs('medicine.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add Medicine</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="{{ request()->routeIs('category.index') ? 'active' : '' }}">
                                        <a href="{{ route('category.index') }}" class="{{ request()->routeIs('category.index') ? 'active' : '' }}">
                                            <i class="fa fa-tags icons"></i>
                                            <span>Categories</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('brand.index') ? 'active' : '' }}">
                                        <a href="{{ route('brand.index') }}" class="{{ request()->routeIs('brand.index') ? 'active' : '' }}">
                                            <i class="fa fa-certificate icons"></i>
                                            <span>Brands</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('medicine_unit.index') ? 'active' : '' }}">
                                        <a href="{{ route('medicine_unit.index') }}" class="{{ request()->routeIs('medicine_unit.index') ? 'active' : '' }}">
                                            <i class="fa fa-balance-scale icons"></i>
                                            <span>Stock Units</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(11, 'view'))
                                        <li class="{{ request()->routeIs('symptoms.index') ? 'active' : '' }}">
                                            <a href="{{ route('symptoms.index') }}" class="{{ request()->routeIs('symptoms.index') ? 'active' : '' }}">
                                                <i class="fa fa-heartbeat icons"></i>
                                                <span>Symptoms</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Followup --}}
                        @if (tenant_has_module('Followup') && app('hasPermission')(2, 'view'))
                            <li class="submenu sidebar-module {{ request()->routeIs('followup.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('followup.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-user-clock icons"></i>
                                    <span>Followup</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('followup.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (tenant_has_module('Followup') && app('hasPermission')(2, 'view'))
                                        <li class="{{ request()->routeIs('followup.index') ? 'active' : '' }}">
                                            <a href="{{ route('followup.index') }}" class="{{ request()->routeIs('followup.index') ? 'active' : '' }}">
                                                <i class="fa fa-users icons"></i>
                                                <span>All Followup</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(2, 'create'))
                                        <li class="{{ request()->routeIs('followup.create') ? 'active' : '' }}">
                                            <a href="{{ route('followup.create') }}" class="{{ request()->routeIs('followup.create') ? 'active' : '' }}">
                                                <i class="fa fa-user-plus icons"></i>
                                                <span>Add Followup</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Staffs --}}
                        @if (tenant_has_module('Staffs') && app('hasPermission')(3, 'view'))
                            <li class="submenu sidebar-module {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('user.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-user-md icons"></i>
                                    <span>Staffs</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('user.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (tenant_has_module('Staffs') && app('hasPermission')(3, 'view'))
                                        <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                                            <a href="{{ route('user.index') }}" class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                                                <i class="fa fa-users icons"></i>
                                                <span>All Staffs</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(3, 'create'))
                                        <li class="{{ request()->routeIs('user.create') ? 'active' : '' }}">
                                            <a href="{{ route('user.create') }}" class="{{ request()->routeIs('user.create') ? 'active' : '' }}">
                                                <i class="fa fa-user-plus icons"></i>
                                                <span>Add Staff</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Sale Bill --}}
                        @if (tenant_has_module('Sale Bill') && (app('hasPermission')(20, 'view') || app('hasPermission')(18, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('sales.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-shopping-cart icons"></i>
                                    <span>Sale Bill</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('sales.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('sales.list') ? 'active' : '' }}">
                                        <a href="{{ route('sales.list') }}" class="{{ request()->routeIs('sales.list') ? 'active' : '' }}">
                                            <i class="fa fa-list-alt icons"></i>
                                            <span>All Sale Bills</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(20, 'create'))
                                        <li class="{{ request()->routeIs('sales.add') ? 'active' : '' }}">
                                            <a href="{{ route('sales.add') }}" class="{{ request()->routeIs('sales.add') ? 'active' : '' }}">
                                                <i class="fa fa-cart-plus icons"></i>
                                                <span>New Sale Bill</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Purchase Bill --}}
                        @if (tenant_has_module('Purchase Bill') && (app('hasPermission')(21, 'view') || app('hasPermission')(11, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('purchase.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('purchase.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-file-invoice icons"></i>
                                    <span>Purchase Bill</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('purchase.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('purchase.lists') ? 'active' : '' }}">
                                        <a href="{{ route('purchase.lists') }}" class="{{ request()->routeIs('purchase.lists') ? 'active' : '' }}">
                                            <i class="fa fa-list icons"></i>
                                            <span>All Purchase Bills</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(21, 'create'))
                                        <li class="{{ request()->routeIs('purchase.add') ? 'active' : '' }}">
                                            <a href="{{ route('purchase.add') }}" class="{{ request()->routeIs('purchase.add') ? 'active' : '' }}">
                                                <i class="fa fa-plus icons"></i>
                                                <span>New Purchase Bill</span>
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('purchase.ai-bill') ? 'active' : '' }}">
                                            <a href="{{ route('purchase.ai-bill') }}" class="{{ request()->routeIs('purchase.ai-bill') ? 'active' : '' }}">
                                                <i class="fa fa-magic icons" style="color: #f97316;"></i>
                                                <span style="color: #f97316; font-weight: 700;">AI Bill Upload</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Vendors (Renamed from Supplier) --}}
                        @if (tenant_has_module('Vendors') && (app('hasPermission')(24, 'view') || app('hasPermission')(12, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('supplier.*', 'vendor.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('supplier.*', 'vendor.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-handshake icons"></i>
                                    <span>Vendors</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('supplier.*', 'vendor.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('supplier.index', 'vendor.index') ? 'active' : '' }}">
                                        <a href="{{ route('supplier.index') }}" class="{{ request()->routeIs('supplier.index', 'vendor.index') ? 'active' : '' }}">
                                            <i class="fa fa-truck icons"></i>
                                            <span>All Vendors</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(24, 'create'))
                                        <li class="{{ request()->routeIs('supplier.create', 'vendor.create') ? 'active' : '' }}">
                                            <a href="{{ route('supplier.create') }}" class="{{ request()->routeIs('supplier.create', 'vendor.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add Vendor</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Treatments --}}
                        @if (tenant_has_module('Treatments') && (app('hasPermission')(9, 'view') || app('hasPermission')(7, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('treatment.*', 'treatment_booking.*', 'machine.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('treatment.*', 'treatment_booking.*', 'machine.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-user-md icons"></i>
                                    <span>Treatments</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('treatment.*', 'treatment_booking.*', 'machine.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (tenant_has_module('Treatments') && (app('hasPermission')(9, 'view') || app('hasPermission')(7, 'view')))
                                        <li class="{{ request()->routeIs('treatment.index') ? 'active' : '' }}">
                                            <a href="{{ route('treatment.index') }}" class="{{ request()->routeIs('treatment.index') ? 'active' : '' }}">
                                                <i class="fa fa-notes-medical icons"></i>
                                                <span>All Treatments</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(9, 'create'))
                                        <li class="{{ request()->routeIs('treatment.create') ? 'active' : '' }}">
                                            <a href="{{ route('treatment.create') }}" class="{{ request()->routeIs('treatment.create') ? 'active' : '' }}">
                                                <i class="fa fa-file-medical icons"></i>
                                                <span>Add Treatment</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(10, 'view'))
                                        <li class="{{ request()->routeIs('treatment_booking.index') ? 'active' : '' }}">
                                            <a href="{{ route('treatment_booking.index') }}" class="{{ request()->routeIs('treatment_booking.index') ? 'active' : '' }}">
                                                <i class="fa fa-calendar-plus icons"></i>
                                                <span>Book Treatment</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(8, 'view'))
                                        <li class="{{ request()->routeIs('machine.index') ? 'active' : '' }}">
                                            <a href="{{ route('machine.index') }}" class="{{ request()->routeIs('machine.index') ? 'active' : '' }}">
                                                <i class="fa fa-cogs icons"></i>
                                                <span>All Machines</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                       

                    </ul>
                </li>
                @endif

                {{-- 3. CLINICAL SERVICES (New Dedicated Menu Created After ERP) --}}
                @if ($showClinicalSection)
                <li class="submenu sidebar-category sidebar-category-clinical {{ $isClinicalActive ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="{{ $isClinicalActive ? 'subdrop active' : '' }}">
                        <i class="fas fa-hospital text-warning"></i>
                        <span>Clinical Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ $isClinicalActive ? 'display: block;' : 'display: none;' }}">

                        {{-- OPD --}}
                        @if (tenant_has_module('OPD') && (app('hasPermission')(13, 'view') || app('hasPermission')(23, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('opd_visit.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('opd_visit.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-plus-square icons"></i>
                                    <span>OPD</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('opd_visit.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('opd_visit.index') ? 'active' : '' }}">
                                        <a href="{{ route('opd_visit.index') }}" class="{{ request()->routeIs('opd_visit.index') ? 'active' : '' }}">
                                            <i class="fa fa-stethoscope icons"></i>
                                            <span>All OPD</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(13, 'create'))
                                        <li class="{{ request()->routeIs('opd_visit.create') ? 'active' : '' }}">
                                            <a href="{{ route('opd_visit.create') }}" class="{{ request()->routeIs('opd_visit.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add OPD</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- IPD --}}
                        @if (tenant_has_module('IPD') && (app('hasPermission')(14, 'view') || app('hasPermission')(25, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('ipd_admit.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('ipd_admit.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-procedures icons"></i>
                                    <span>IPD</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('ipd_admit.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('ipd_admit.index') ? 'active' : '' }}">
                                        <a href="{{ route('ipd_admit.index') }}" class="{{ request()->routeIs('ipd_admit.index') ? 'active' : '' }}">
                                            <i class="fa fa-bed icons"></i>
                                            <span>All IPD</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(14, 'create'))
                                        <li class="{{ request()->routeIs('ipd_admit.create') ? 'active' : '' }}">
                                            <a href="{{ route('ipd_admit.create') }}" class="{{ request()->routeIs('ipd_admit.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add IPD</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Daily Register --}}
                        @if (tenant_has_module('Daily Register') && (app('hasPermission')(30, 'view') || app('hasPermission')(28, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('daily_data.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('daily_data.*') ? 'subdrop active' : '' }}">
                                    <i class="fas fa-file-medical-alt icons"></i>
                                    <span>Daily Register</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('daily_data.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('daily_data.index') ? 'active' : '' }}">
                                        <a href="{{ route('daily_data.index') }}" class="{{ request()->routeIs('daily_data.index') ? 'active' : '' }}">
                                            <i class="fas fa-list-alt icons"></i>
                                            <span>All Daily Register</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(30, 'create'))
                                        <li class="{{ request()->routeIs('daily_data.create') ? 'active' : '' }}">
                                            <a href="{{ route('daily_data.create') }}" class="{{ request()->routeIs('daily_data.create') ? 'active' : '' }}">
                                                <i class="fas fa-plus-circle icons"></i>
                                                <span>Add Daily Register</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Therapy --}}
                        @if (tenant_has_module('Therapy') && (app('hasPermission')(33, 'view') || app('hasPermission')(26, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('therapy.*', 'assign-therapy.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('therapy.*', 'assign-therapy.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-hand-holding-medical icons"></i>
                                    <span>Therapy</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('therapy.*', 'assign-therapy.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('therapy.index') ? 'active' : '' }}">
                                        <a href="{{ route('therapy.index') }}" class="{{ request()->routeIs('therapy.index') ? 'active' : '' }}">
                                            <i class="fa fa-list-alt icons"></i>
                                            <span>All Therapy</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(33, 'create'))
                                        <li class="{{ request()->routeIs('therapy.create') ? 'active' : '' }}">
                                            <a href="{{ route('therapy.create') }}" class="{{ request()->routeIs('therapy.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add Therapy</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="{{ request()->routeIs('assign-therapy.index') ? 'active' : '' }}">
                                        <a href="{{ route('assign-therapy.index') }}" class="{{ request()->routeIs('assign-therapy.index') ? 'active' : '' }}">
                                            <i class="fa fa-user-check icons"></i>
                                            <span>Assign Therapy</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- Diet Chart --}}
                        @if (tenant_has_module('Diet Chart') && app('hasPermission')(34, 'view'))
                            <li class="submenu sidebar-module {{ request()->routeIs('dietchart.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('dietchart.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-apple-alt icons"></i>
                                    <span>Diet Chart</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('dietchart.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('dietchart.index') ? 'active' : '' }}">
                                        <a href="{{ route('dietchart.index') }}" class="{{ request()->routeIs('dietchart.index') ? 'active' : '' }}">
                                            <i class="fa fa-list-alt icons"></i>
                                            <span>All Diet Charts</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(34, 'create'))
                                        <li class="{{ request()->routeIs('dietchart.create') ? 'active' : '' }}">
                                            <a href="{{ route('dietchart.create') }}" class="{{ request()->routeIs('dietchart.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add Diet Chart</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Clinical Notes --}}
                        @if (tenant_has_module('Clinical Notes') && (app('hasPermission')(28, 'view') || app('hasPermission')(27, 'view') || app('hasPermission')(31, 'view') || app('hasPermission')(32, 'view') || app('hasPermission')(33, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('assessment.*', 'homeadvice.*', 'soap.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('assessment.*', 'homeadvice.*', 'soap.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-notes-medical icons"></i>
                                    <span>Clinical Notes</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('assessment.*', 'homeadvice.*', 'soap.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (app('hasPermission')(28, 'view'))
                                        <li class="{{ request()->routeIs('assessment.index') ? 'active' : '' }}">
                                            <a href="{{ route('assessment.index') }}" class="{{ request()->routeIs('assessment.index') ? 'active' : '' }}">
                                                <i class="fa fa-clipboard-check icons"></i>
                                                <span>Assessment</span>
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('homeadvice.index') ? 'active' : '' }}">
                                            <a href="{{ route('homeadvice.index') }}" class="{{ request()->routeIs('homeadvice.index') ? 'active' : '' }}">
                                                <i class="fa fa-home icons"></i>
                                                <span>Home Advice</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(27, 'view'))
                                        <li class="{{ request()->routeIs('soap.index') ? 'active' : '' }}">
                                            <a href="{{ route('soap.index') }}" class="{{ request()->routeIs('soap.index') ? 'active' : '' }}">
                                                <i class="fa fa-comment-medical icons"></i>
                                                <span>SOAP</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Diagnostic Services --}}
                        @if (tenant_has_module('Diagnostic Service') && (app('hasPermission')(17, 'view') || app('hasPermission')(18, 'view') || app('hasPermission')(19, 'view') || app('hasPermission')(8, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('pathology.*', 'pathology_reports.*', 'radiology-tests.*', 'radiology-reports.*', 'service.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('pathology.*', 'pathology_reports.*', 'radiology-tests.*', 'radiology-reports.*', 'service.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-microscope icons"></i>
                                    <span>Diagnostic Service</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('pathology.*', 'pathology_reports.*', 'radiology-tests.*', 'radiology-reports.*', 'service.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (app('hasPermission')(19, 'view'))
                                        <li class="{{ request()->routeIs('service.index') ? 'active' : '' }}">
                                            <a href="{{ route('service.index') }}" class="{{ request()->routeIs('service.index') ? 'active' : '' }}">
                                                <i class="fa fa-cogs icons"></i>
                                                <span>Service Master</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(17, 'view'))
                                        <li class="{{ request()->routeIs('pathology.index') ? 'active' : '' }}">
                                            <a href="{{ route('pathology.index') }}" class="{{ request()->routeIs('pathology.index') ? 'active' : '' }}">
                                                <i class="fa fa-vial icons"></i>
                                                <span>Pathology Tests</span>
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('pathology_reports.index') ? 'active' : '' }}">
                                            <a href="{{ route('pathology_reports.index') }}" class="{{ request()->routeIs('pathology_reports.index') ? 'active' : '' }}">
                                                <i class="fa fa-file-alt icons"></i>
                                                <span>Pathology Reports</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(18, 'view'))
                                        <li class="{{ request()->routeIs('radiology-tests.index') ? 'active' : '' }}">
                                            <a href="{{ route('radiology-tests.index') }}" class="{{ request()->routeIs('radiology-tests.index') ? 'active' : '' }}">
                                                <i class="fa fa-x-ray icons"></i>
                                                <span>Radiology Tests</span>
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('radiology-reports.index') ? 'active' : '' }}">
                                            <a href="{{ route('radiology-reports.index') }}" class="{{ request()->routeIs('radiology-reports.index') ? 'active' : '' }}">
                                                <i class="fa fa-file-medical icons"></i>
                                                <span>Radiology Reports</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Medical Reports --}}
                        @if (tenant_has_module('Medical Report') && (app('hasPermission')(25, 'view') || app('hasPermission')(13, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('report.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('report.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-file-medical icons"></i>
                                    <span>Medical Report</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('report.*') ? 'display: block;' : 'display: none;' }}">
                                    <li class="{{ request()->routeIs('report.index') ? 'active' : '' }}">
                                        <a href="{{ route('report.index') }}" class="{{ request()->routeIs('report.index') ? 'active' : '' }}">
                                            <i class="fa fa-list-alt icons"></i>
                                            <span>All Medical Reports</span>
                                        </a>
                                    </li>
                                    @if (app('hasPermission')(25, 'create'))
                                        <li class="{{ request()->routeIs('report.create') ? 'active' : '' }}">
                                            <a href="{{ route('report.create') }}" class="{{ request()->routeIs('report.create') ? 'active' : '' }}">
                                                <i class="fa fa-plus-circle icons"></i>
                                                <span>Add Medical Report</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- OT & Discharge --}}
                        @if (tenant_has_module('OT & Discharge') && (app('hasPermission')(15, 'view') || app('hasPermission')(16, 'view') || app('hasPermission')(10, 'view') || app('hasPermission')(24, 'view')))
                            <li class="submenu sidebar-module {{ request()->routeIs('ot.*', 'discharge.*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ request()->routeIs('ot.*', 'discharge.*') ? 'subdrop active' : '' }}">
                                    <i class="fa fa-heartbeat icons"></i>
                                    <span>OT & Discharge</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ request()->routeIs('ot.*', 'discharge.*') ? 'display: block;' : 'display: none;' }}">
                                    @if (app('hasPermission')(15, 'view'))
                                        <li class="{{ request()->routeIs('ot.index') ? 'active' : '' }}">
                                            <a href="{{ route('ot.index') }}" class="{{ request()->routeIs('ot.index') ? 'active' : '' }}">
                                                <i class="fa fa-procedures icons"></i>
                                                <span>OT Procedure</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (app('hasPermission')(16, 'view'))
                                        <li class="{{ request()->routeIs('discharge.index') ? 'active' : '' }}">
                                            <a href="{{ route('discharge.index') }}" class="{{ request()->routeIs('discharge.index') ? 'active' : '' }}">
                                                <i class="fa fa-sign-out-alt icons"></i>
                                                <span>Discharge</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                    </ul>
                </li>

                @endif

                {{-- 4. ACCOUNTING (Includes Account Ledger, Stock / Inventory, Expenses, Invoices) --}}
                @if ($showAccountingSection && (app('hasPermission')(22, 'view') || app('hasPermission')(23, 'view') || app('hasPermission')(26, 'view') || app('hasPermission')(31, 'view')))
                    <li class="submenu sidebar-category sidebar-category-accounting {{ $isAccountingActive ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="{{ $isAccountingActive ? 'subdrop active' : '' }}">
                            <i class="fas fa-calculator text-success"></i>
                            <span>Accounting</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="{{ $isAccountingActive ? 'display: block;' : 'display: none;' }}">
                            {{-- Account Ledger --}}
                            @if (tenant_has_module('Account Ledger') && app('hasPermission')(31, 'view'))
                                <li class="sidebar-module {{ request()->routeIs('account_ledger.*') ? 'active' : '' }}">
                                    <a href="{{ route('account_ledger.index') }}" class="{{ request()->routeIs('account_ledger.index') ? 'active' : '' }}">
                                        <i class="fas fa-book icons"></i>
                                        <span>Account Ledger</span>
                                    </a>
                                </li>
                            @endif

                            {{-- Stock / Inventory (Moved to Accounting per instruction) --}}
                            @if (tenant_has_module('Stock / Inventory') && (app('hasPermission')(23, 'view') || app('hasPermission')(11, 'view')))
                                <li class="sidebar-module {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                                        <i class="fas fa-warehouse icons"></i>
                                        <span>Stock / Inventory</span>
                                    </a>
                                </li>
                            @endif

                            {{-- Expenses --}}
                            @if (tenant_has_module('Expenses') && (app('hasPermission')(22, 'view') || app('hasPermission')(5, 'view')))
                                <li class="submenu sidebar-module {{ request()->routeIs('expense.*') ? 'active' : '' }}">
                                    <a href="javascript:void(0);" class="{{ request()->routeIs('expense.*') ? 'subdrop active' : '' }}">
                                        <i class="fas fa-money-bill-wave icons"></i>
                                        <span>Expenses</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('expense.*') ? 'display: block;' : 'display: none;' }}">
                                        <li class="{{ request()->routeIs('expense.index') ? 'active' : '' }}">
                                            <a href="{{ route('expense.index') }}" class="{{ request()->routeIs('expense.index') ? 'active' : '' }}">
                                                <i class="fa fa-list-alt icons"></i>
                                                <span>All Expenses</span>
                                            </a>
                                        </li>
                                        @if (app('hasPermission')(22, 'create'))
                                            <li class="{{ request()->routeIs('expense.create') ? 'active' : '' }}">
                                                <a href="{{ route('expense.create') }}" class="{{ request()->routeIs('expense.create') ? 'active' : '' }}">
                                                    <i class="fa fa-plus-circle icons"></i>
                                                    <span>Add Expense</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            {{-- Invoices --}}
                            @if (tenant_has_module('Invoices') && (app('hasPermission')(26, 'view') || app('hasPermission')(18, 'view')))
                                <li class="sidebar-module {{ request()->routeIs('invoice.*') ? 'active' : '' }}">
                                    <a href="{{ route('invoice.index') }}" class="{{ request()->routeIs('invoice.index') ? 'active' : '' }}">
                                        <i class="fas fa-file-invoice-dollar icons"></i>
                                        <span>Invoices</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- 5. REPORTS --}}
                @if ($showReportsSection)
                <li class="submenu sidebar-category sidebar-category-reports {{ $isReportsActive ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="{{ $isReportsActive ? 'subdrop active' : '' }}">
                        <i class="fas fa-chart-line" style="color: #6f42c1;"></i>
                        <span>Reports</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ $isReportsActive ? 'display: block;' : 'display: none;' }}">
                        @if (tenant_has_module(['Sales Report', 'sales-report', 'Reports', 'Report']))
                            <li class="sidebar-module {{ request()->routeIs('sales.report') || request()->is('sales-report*') ? 'active' : '' }}">
                                <a href="{{ Route::has('sales.report') ? route('sales.report') : url('/sales-report') }}" class="{{ request()->routeIs('sales.report') || request()->is('sales-report*') ? 'active' : '' }}">
                                    <i class="fa fa-chart-bar icons"></i>
                                    <span>Sales Report</span>
                                </a>
                            </li>
                        @endif
                        @if (tenant_has_module(['Purchase Report', 'purchase-report', 'Reports', 'Report']))
                            <li class="sidebar-module {{ request()->routeIs('purchase.report') || request()->is('purchase-report*') ? 'active' : '' }}">
                                <a href="{{ Route::has('purchase.report') ? route('purchase.report') : url('/purchase-report') }}" class="{{ request()->routeIs('purchase.report') || request()->is('purchase-report*') ? 'active' : '' }}">
                                    <i class="fa fa-chart-pie icons"></i>
                                    <span>Purchase Report</span>
                                </a>
                            </li>
                        @endif
                        @if (tenant_has_module(['Expense Report', 'expense-report', 'Expenses Report', 'Reports', 'Report']))
                            <li class="sidebar-module {{ request()->routeIs('expense.report') || request()->is('expense-report*') ? 'active' : '' }}">
                                <a href="{{ Route::has('expense.report') ? route('expense.report') : url('/expense-report') }}" class="{{ request()->routeIs('expense.report') || request()->is('expense-report*') ? 'active' : '' }}">
                                    <i class="fa fa-file-invoice-dollar icons"></i>
                                    <span>Expense Report</span>
                                </a>
                            </li>
                        @endif
                        @if (tenant_has_module(['Analytics & Summary', 'Reports', 'Report']) && (app('hasPermission')(32, 'view') || app('hasPermission')(40, 'view') || app('hasPermission')(14, 'view') || (auth()->user() && in_array((string)auth()->user()->role_id, ['1', '2']))))
                            <li class="sidebar-module {{ request()->routeIs('chart') || request()->is('chart*') ? 'active' : '' }}">
                                <a href="{{ Route::has('chart') ? route('chart') : url('/chart') }}" class="{{ request()->routeIs('chart') || request()->is('chart*') ? 'active' : '' }}">
                                    <i class="fa fa-chart-area icons"></i>
                                    <span>Analytics & Summary</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @endif

                {{-- 6. SETTINGS --}}
                <li class="submenu sidebar-category sidebar-category-settings {{ $isSettingsActive ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="{{ $isSettingsActive ? 'subdrop active' : '' }}">
                        <i class="fas fa-cog text-info"></i>
                        <span>Settings</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ $isSettingsActive ? 'display: block;' : 'display: none;' }}">
                        @if (tenant_has_module('Settings') && (app('hasPermission')(4, 'view') || app('hasPermission')(15, 'view')))
                            <li class="sidebar-module {{ request()->routeIs('sms.template') ? 'active' : '' }}">
                                <a href="{{ route('sms.template') }}" class="{{ request()->routeIs('sms.template') ? 'active' : '' }}">
                                    <i class="fa fa-store icons"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                        @endif
                        @if (tenant_has_module('Tax Rates') && (app('hasPermission')(21, 'view') || app('hasPermission')(35, 'view')) && Route::has('taxrate.index'))
                            <li class="sidebar-module {{ request()->routeIs('taxrate.index') ? 'active' : '' }}">
                                <a href="{{ route('taxrate.index') }}" class="{{ request()->routeIs('taxrate.index') ? 'active' : '' }}">
                                    <i class="fa fa-percent icons"></i>
                                    <span>Tax Rates</span>
                                </a>
                            </li>
                        @endif
                        @if (tenant_has_module('My Plan Details') && Route::has('plans.myplan'))
                            <li class="sidebar-module {{ request()->routeIs('plans.myplan') ? 'active' : '' }}">
                                <a href="{{ route('plans.myplan') }}" class="{{ request()->routeIs('plans.myplan') ? 'active' : '' }}">
                                    <i class="fa fa-crown icons"></i>
                                    <span>My Plan Details</span>
                                </a>
                            </li>
                        @endif
                     
                        @if (tenant_has_module('Calendar') && Route::has('calender.index'))
                            <li class="sidebar-module {{ request()->routeIs('calender.index') ? 'active' : '' }}">
                                <a href="{{ route('calender.index') }}" class="{{ request()->routeIs('calender.index') ? 'active' : '' }}">
                                    <i class="fa fa-calendar-alt icons"></i>
                                    <span>Calendar</span>
                                </a>
                            </li>
                        @endif
                        @if (Route::has('knowledge.index') && tenant_has_module('Knowledge Base'))
                        <li class="sidebar-module {{ request()->routeIs('knowledge.index') ? 'active' : '' }}">
                                <a href="{{ route('knowledge.index') }}" class="{{ request()->routeIs('knowledge.index') ? 'active' : '' }}">
                                    <i class="fa fa-book-open icons"></i>
                                    <span>Knowledge Base</span>
                                </a>
                            </li>
                        @endif
                        @if (Route::has('profile'))
                            <li class="sidebar-module {{ request()->routeIs('profile') ? 'active' : '' }}">
                                <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                                    <i class="fa fa-user-circle icons"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>
                        @endif
                        @if (Route::has('logout'))
                            <li class="sidebar-module">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                    <i class="fa fa-sign-out-alt icons text-danger"></i>
                                    <span class="text-danger">Logout</span>
                                </a>
                                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endif
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>

<!-- Multi-level Accordion and Auto-Expansion Script -->
<script>
    (function() {
        window.initSidebarMenu = function() {
            if (!window.jQuery) {
                setTimeout(window.initSidebarMenu, 50);
                return;
            }
            var $ = window.jQuery;

            // Remove all existing click handlers to ensure only one clean handler is active
            $('#sidebar-menu a').off('click');

            // Multi-level accordion handler
            $('#sidebar-menu a').on('click', function(e) {
                var $a = $(this);
                var $parentLi = $a.parent('li');

                if ($parentLi.hasClass('submenu')) {
                    e.preventDefault();
                    e.stopPropagation();

                    var $nextUl = $a.next('ul');
                    // Check if actually open (both subdrop and visible)
                    var isOpen = $a.hasClass('subdrop') && ($nextUl.is(':visible') || $nextUl.css('display') === 'block');

                    if (isOpen) {
                        // Close this item
                        $a.removeClass('subdrop');
                        $nextUl.slideUp(200);
                    } else {
                        // Open this item
                        if ($parentLi.hasClass('sidebar-category')) {
                            // Top category: close sibling categories only
                            $('#sidebar-menu > ul > li.sidebar-category > a.subdrop').not($a).each(function() {
                                $(this).removeClass('subdrop');
                                $(this).next('ul').slideUp(200);
                            });
                        } else {
                            // Sub-module: close sibling sub-modules within the SAME category only
                            $parentLi.siblings('li.submenu').children('a.subdrop').each(function() {
                                $(this).removeClass('subdrop');
                                $(this).next('ul').slideUp(200);
                            });
                        }

                        $a.addClass('subdrop');
                        $nextUl.slideDown(200);
                    }
                }
            });

            // Ensure active items are expanded on page load
            $('#sidebar-menu a.active').each(function() {
                var $activeA = $(this);
                $activeA.parents('ul').show();
                $activeA.parents('li.submenu').each(function() {
                    $(this).addClass('active');
                    $(this).children('a:first').addClass('subdrop active');
                });
            });

            // Real-time search filter
            var searchInput = document.getElementById('sidebar-module-search');
            if (searchInput && !searchInput.dataset.bound) {
                searchInput.dataset.bound = "1";
                searchInput.addEventListener('keyup', function() {
                    var filter = this.value.toLowerCase().trim();
                    var menuItems = document.querySelectorAll('#sidebar-menu ul li a');

                    if (filter === '') {
                        document.querySelectorAll('#sidebar-menu ul li').forEach(function(li) {
                            li.style.display = '';
                        });
                        // Re-open active hierarchy
                        $('#sidebar-menu a.active').each(function() {
                            $(this).parents('ul').show();
                        });
                        return;
                    }

                    menuItems.forEach(function(item) {
                        var text = item.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            item.parentElement.style.display = '';
                            var parent = item.parentElement.parentElement.closest('li');
                            while (parent) {
                                parent.style.display = '';
                                var parentUl = parent.querySelector('ul');
                                if (parentUl) parentUl.style.display = 'block';
                                parent = parent.parentElement.closest('li');
                            }
                        } else {
                            if (!item.nextElementSibling || item.nextElementSibling.tagName !== 'UL') {
                                item.parentElement.style.display = 'none';
                            }
                        }
                    });

                    document.querySelectorAll('#sidebar-menu .submenu').forEach(function(submenu) {
                        var visibleItems = submenu.querySelectorAll('ul li[style=""]');
                        if (visibleItems.length === 0) {
                            submenu.style.display = 'none';
                        } else {
                            submenu.style.display = '';
                        }
                    });
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initSidebarMenu);
        } else {
            window.initSidebarMenu();
        }
    })();
</script>