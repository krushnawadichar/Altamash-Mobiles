<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ \App\Models\Setting::get('shop_name', 'MobileCare POS') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --primary: #4338ca;
            --primary-hover: #3730a3;
            --primary-light: #eef2ff;
            --sidebar-bg: #1e1e2f;
            --sidebar-hover: #2b2b40;
            --sidebar-active: #4f46e5;
            --sidebar-text: #94a3b8;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Layout Structure */
        #app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: #fff;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1040;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            margin-left: -260px;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #fff;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #4338ca);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .sidebar-brand-text {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-menu-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #64748b;
            padding: 0.85rem 0.75rem 0.35rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .nav-link-custom i {
            font-size: 1.15rem;
            width: 20px;
            text-align: center;
        }

        .nav-link-custom:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
        }

        .nav-link-custom.active {
            color: #fff;
            background: linear-gradient(90deg, #4f46e5, #4338ca);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
        }

        .nav-link-custom .badge {
            margin-left: auto;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        /* Main Content Container */
        #main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Navbar */
        #topbar {
            background-color: #fff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .topbar-search {
            position: relative;
            max-width: 320px;
            width: 100%;
        }

        .topbar-search input {
            padding-left: 2.25rem;
            border-radius: 20px;
            font-size: 0.85rem;
            background-color: #f1f5f9;
            border: 1px solid transparent;
        }

        .topbar-search input:focus {
            background-color: #fff;
            border-color: #cbd5e1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .topbar-search i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Content Area */
        .content-body {
            padding: 1.5rem;
            flex: 1;
        }

        /* Cards & Styling */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 1.25rem;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Notification Dropdown */
        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Custom Badges */
        .badge-pos {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            font-weight: 700;
        }

        /* DataTables Spacing Fix */
        .dataTables_wrapper {
            padding: 1.25rem;
        }
        .dataTables_wrapper .row:first-child {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .row:last-child {
            margin-top: 1rem;
        }
        .table-responsive {
            margin-bottom: 0;
        }
        .dataTables_wrapper .table {
            margin-bottom: 0 !important;
            border-bottom: 1px solid var(--card-border);
        }

        @media (max-width: 992px) {
            #sidebar {
                position: fixed;
                height: 100vh;
                margin-left: -260px;
            }
            #sidebar.show {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div id="app-wrapper">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigation -->
        <aside id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <div class="sidebar-brand-text">{{ \App\Models\Setting::get('shop_name', 'MobileCare POS') }}</div>
                    <small style="font-size: 0.68rem; color: #94a3b8;">Shop Management</small>
                </div>
            </a>

            <div class="sidebar-menu">
                <div class="sidebar-menu-title">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.pos.index') }}" class="nav-link-custom {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}">
                    <i class="bi bi-calculator-fill text-warning"></i>
                    <span>POS Terminal</span>
                    <span class="badge badge-pos">HOT</span>
                </a>

                <div class="sidebar-menu-title">Inventory & Catalog</div>
                <a href="{{ route('admin.products.index') }}" class="nav-link-custom {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="nav-link-custom {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>Stock Overview</span>
                </a>
                <a href="{{ route('admin.inventory.imeis') }}" class="nav-link-custom {{ request()->routeIs('admin.inventory.imeis') ? 'active' : '' }}">
                    <i class="bi bi-upc-scan"></i>
                    <span>IMEI Tracker</span>
                </a>
                <a href="{{ route('admin.inventory.transactions') }}" class="nav-link-custom {{ request()->routeIs('admin.inventory.transactions') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Stock Ledger</span>
                </a>
                <a href="{{ route('admin.inventory.adjustments.index') }}" class="nav-link-custom {{ request()->routeIs('admin.inventory.adjustments.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span>Adjustments</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="nav-link-custom {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.brands.index') }}" class="nav-link-custom {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="bi bi-award-fill"></i>
                    <span>Brands</span>
                </a>

                <div class="sidebar-menu-title">Purchases & Suppliers</div>
                <a href="{{ route('admin.purchases.index') }}" class="nav-link-custom {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check-fill"></i>
                    <span>Purchases</span>
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="nav-link-custom {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Suppliers</span>
                </a>

                <div class="sidebar-menu-title">Sales & Billing</div>
                <a href="{{ route('admin.sales.index') }}" class="nav-link-custom {{ request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.show') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Sales Invoices</span>
                </a>
                <a href="{{ route('admin.sales.returns.index') }}" class="nav-link-custom {{ request()->routeIs('admin.sales.returns.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Sales Returns</span>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="nav-link-custom {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Customers</span>
                </a>

                <div class="sidebar-menu-title">Mobile Repairing</div>
                <a href="{{ route('admin.repairs.index') }}" class="nav-link-custom {{ request()->routeIs('admin.repairs.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>
                    <span>Repair Jobs</span>
                    @php
                        $activeRepairsCount = \App\Models\RepairJob::whereNotIn('status', ['Delivered', 'Cancelled'])->count();
                    @endphp
                    @if($activeRepairsCount > 0)
                        <span class="badge bg-danger">{{ $activeRepairsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.technicians.index') }}" class="nav-link-custom {{ request()->routeIs('admin.technicians.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Technicians</span>
                </a>

                <div class="sidebar-menu-title">Finance & Reports</div>
                <a href="{{ route('admin.expenses.index') }}" class="nav-link-custom {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Expenses</span>
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Sales Report</span>
                </a>
                <a href="{{ route('admin.reports.purchases') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.purchases') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i>
                    <span>Purchase Report</span>
                </a>
                <a href="{{ route('admin.reports.inventory') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span>Stock Report</span>
                </a>
                <a href="{{ route('admin.reports.mobiles') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.mobiles') ? 'active' : '' }}">
                    <i class="bi bi-phone-vibrate"></i>
                    <span>Mobiles & IMEIs</span>
                </a>
                <a href="{{ route('admin.reports.repairs') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.repairs') ? 'active' : '' }}">
                    <i class="bi bi-wrench-adjustable"></i>
                    <span>Repair Report</span>
                </a>
                <a href="{{ route('admin.reports.profit-loss') }}" class="nav-link-custom {{ request()->routeIs('admin.reports.profit-loss') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill text-success"></i>
                    <span>Profit & Loss</span>
                </a>

                @if(auth()->user()->isAdmin())
                <div class="sidebar-menu-title">Administration</div>
                <a href="{{ route('admin.users.index') }}" class="nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Staff & Roles</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link-custom {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Shop Settings</span>
                </a>
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link-custom {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Audit Logs</span>
                </a>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <div id="main-content">
            <!-- Topbar Header -->
            <header id="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light border-0 shadow-none d-lg-none" id="sidebarToggleBtn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="d-none d-md-block">
                        <h5 class="mb-0 fw-bold" style="letter-spacing: -0.02em;">@yield('page_title', 'Dashboard')</h5>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Quick Action POS -->
                    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-3 py-1 fw-bold">
                        <i class="bi bi-plus-circle"></i>
                        <span>POS Billing</span>
                    </a>

                    <a href="{{ route('admin.repairs.create') }}" class="btn btn-outline-dark btn-sm d-none d-sm-flex align-items-center gap-1 px-3 py-1">
                        <i class="bi bi-tools"></i>
                        <span>New Repair</span>
                    </a>

                    <!-- Live Notification Alerts Dropdown -->
                    @php
                        $alertLowStock = \App\Models\Product::whereColumn('current_stock', '<=', 'min_stock')->count();
                        $alertReadyRepairs = \App\Models\RepairJob::where('status', 'Ready for Delivery')->count();
                        $totalAlerts = $alertLowStock + $alertReadyRepairs;
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-light position-relative p-2 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5 text-secondary"></i>
                            @if($totalAlerts > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ $totalAlerts }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow-lg p-0">
                            <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Notifications</h6>
                                <span class="badge bg-primary rounded-pill">{{ $totalAlerts }} Alerts</span>
                            </div>
                            <div class="p-2">
                                @if($alertLowStock > 0)
                                <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="dropdown-item p-2 rounded d-flex gap-2 align-items-center text-wrap">
                                    <div class="rounded-circle p-2 bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $alertLowStock }} Products Low in Stock</div>
                                        <small class="text-muted">Reorder inventory soon</small>
                                    </div>
                                </a>
                                @endif

                                @if($alertReadyRepairs > 0)
                                <a href="{{ route('admin.repairs.index', ['status' => 'Ready for Delivery']) }}" class="dropdown-item p-2 rounded d-flex gap-2 align-items-center text-wrap">
                                    <div class="rounded-circle p-2 bg-success-subtle text-success"><i class="bi bi-check-circle"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $alertReadyRepairs }} Repairs Ready for Delivery</div>
                                        <small class="text-muted">Notify customers for pickup</small>
                                    </div>
                                </a>
                                @endif

                                @if($totalAlerts === 0)
                                    <div class="text-center text-muted p-4">
                                        <i class="bi bi-check2-all fs-2 text-success"></i>
                                        <p class="mb-0 mt-2 small">All caught up! No urgent alerts.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light border d-flex align-items-center gap-2 py-1 px-2 rounded-pill" type="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline fw-semibold small">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down text-muted small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                                <span class="badge bg-secondary mt-1 text-uppercase" style="font-size: 0.65rem;">{{ auth()->user()->role }}</span>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i>Shop Settings</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Page Content Body -->
            <main class="content-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Please check the following errors:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts: jQuery, Bootstrap 5, Select2, DataTables, SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Sidebar Mobile Toggle
            $('#sidebarToggleBtn').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('show');
            });

            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('show');
                $('#sidebarOverlay').removeClass('show');
            });

            // Initialize Select2 elements
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Auto initialize standard tables
            $('.datatable').DataTable({
                pageLength: 25,
                responsive: true
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
