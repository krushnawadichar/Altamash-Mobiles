<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name', 'ALTAMASH MOBILE') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563EB;
            --secondary-color: #1E293B;
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: var(--secondary-color);
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto; /* Allow scrolling if content overflows */
        }
        /* Hide scrollbar visually but keep functionality */
        #sidebar::-webkit-scrollbar {
            width: 5px;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul p {
            color: #fff;
            padding: 10px;
        }
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: #cbd5e1;
            text-decoration: none;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--primary-color);
        }
        #content {
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .nav-link {
            color: #333;
        }
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
            }
            #sidebarCollapse {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="m-0"><img src="{{ asset('build/assets/logos/altmash-logo.jpeg') }}" alt="ALTAMASH MOBILE Logo" style="height: 45px; object-fit: contain;" class="me-2 rounded"> ALTAMASH MOBILE</h4>
            </div>

            <ul class="list-unstyled components">
                @can('view dashboard')
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
                </li>
                @endcan
                
                <!-- Moved POS to Top -->
                @can('view pos')
                <li class="{{ request()->routeIs('admin.pos.*') || request()->routeIs('pos.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.index') }}" class="text-warning fw-bold"><i class="fa-solid fa-cash-register me-2"></i> POS / Sales</a>
                </li>
                @endcan

                @can('view products')
                <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}"><i class="fa-solid fa-box-open me-2"></i> Products</a>
                </li>
                @endcan
                
                @can('view categories')
                <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-tags me-2"></i> Categories</a>
                </li>
                @endcan
                
                @can('view brands')
                <li class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.brands.index') }}"><i class="fa-solid fa-copyright me-2"></i> Brands</a>
                </li>
                @endcan
                
                @can('view inventory')
                <li class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"><a href="{{ route('admin.inventory.index') }}"><i class="fa-solid fa-warehouse me-2"></i> Inventory</a></li>
                @endcan
                
                @can('view purchases')
                <li class="{{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}"><a href="{{ route('admin.purchases.index') }}"><i class="fa-solid fa-truck-field me-2"></i> Purchases</a></li>
                @endcan
                
                @can('view suppliers')
                <li class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"><a href="{{ route('admin.suppliers.index') }}"><i class="fa-solid fa-truck me-2"></i> Suppliers</a></li>
                @endcan
                
                @can('view customers')
                <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><a href="{{ route('admin.customers.index') }}"><i class="fa-solid fa-users me-2"></i> Customers</a></li>
                @endcan
                
                @can('view sales')
                <li class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}"><a href="{{ route('admin.sales.index') }}"><i class="fa-solid fa-cart-shopping me-2"></i> Orders / Invoices</a></li>
                @endcan
                
                @can('view repairs')
                <li class="{{ request()->routeIs('admin.repairs.*') ? 'active' : '' }}"><a href="{{ route('admin.repairs.index') }}"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Repairs</a></li>
                @endcan
                
                @can('view expenses')
                <li class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}"><a href="{{ route('admin.expenses.index') }}"><i class="fa-solid fa-money-bill-wave me-2"></i> Expenses</a></li>
                @endcan
                
                <hr class="text-white-50 mx-3 my-2">
                
                @can('view users')
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}"><i class="fa-solid fa-users me-2"></i> Users</a>
                </li>
                @endcan
                
                @can('view roles')
                <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.index') }}"><i class="fa-solid fa-user-shield me-2"></i> Roles</a>
                </li>
                @endcan
                
                @can('view settings')
                <li class="mb-5 pb-5 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}"><i class="fa-solid fa-cog me-2"></i> Settings</a>
                </li>
                @endcan
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-custom py-2">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary d-lg-none">
                        <i class="fas fa-align-left"></i>
                    </button>
                    
                    <div class="d-flex ms-auto">
                        <ul class="navbar-nav flex-row align-items-center">
                            <li class="nav-item me-3">
                                <a class="nav-link" href="/" target="_blank" title="View Website">
                                    <i class="fa-solid fa-globe"></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-user-circle fs-5"></i> {{ Auth::user()->name ?? 'Admin' }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-sign-out-alt me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required if we use some plugins, though Bootstrap 5 is vanilla JS) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            
            // Initialize all datatables
            if($('.datatable').length > 0) {
                $('.datatable').DataTable({
                    "pageLength": 25,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search records..."
                    },
                    "drawCallback": function() {
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
