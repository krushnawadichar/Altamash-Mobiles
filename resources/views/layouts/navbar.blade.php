<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" id="navbar">
    <div class="container-fluid">
        <button class="btn btn-primary" id="menu-toggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
        
        <span class="navbar-brand ms-3 d-none d-md-block">
            <span class="text-primary fw-bold">{{ config('app.name', 'Altamash Mobiles') }}</span>
        </span>

        <div class="d-flex align-items-center ms-auto">
            <!-- Global Search -->
            <div class="me-3 d-none d-sm-block">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="globalSearch" 
                           placeholder="Search..." style="width: 200px;">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-outline-primary btn-sm position-relative" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                        3
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Low stock alert: iPhone 14 Pro
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        New sale: Invoice #INV-001
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-clock text-info me-2"></i>
                        Pending repair: 5 items
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">View All</a></li>
                </ul>
            </div>

            <!-- User Profile -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" 
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fs-5"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>y