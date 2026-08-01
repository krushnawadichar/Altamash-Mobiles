<nav id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="fas fa-store me-2"></i>Altamash
    </div>
    
    <div class="list-group list-group-flush">
        <a href="{{ route('dashboard') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-cubes"></i> Products
        </div>
        
        <a href="{{ route('admin.categories.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categories
        </a>
        
        <a href="{{ route('admin.brands.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="fas fa-copyright"></i> Brands
        </a>
        
        <a href="{{ route('admin.products.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fas fa-mobile-alt"></i> Products
        </a>
        
        <a href="{{ route('admin.accessories.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.accessories.*') ? 'active' : '' }}">
            <i class="fas fa-headphones"></i> Accessories
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-shopping-cart"></i> Purchases & Sales
        </div>

        <a href="{{ route('admin.purchases.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i> Purchases
        </a>

        <a href="{{ route('admin.sales.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i> Sales
        </a>

        <a href="{{ route('admin.inventory.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> Inventory
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-tools"></i> Services
        </div>

        <a href="{{ route('admin.repairs.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.repairs.*') ? 'active' : '' }}">
            <i class="fas fa-wrench"></i> Repairs
        </a>

        <a href="{{ route('admin.expenses.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> Expenses
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-users"></i> CRM
        </div>

        <a href="{{ route('admin.suppliers.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Suppliers
        </a>

        <a href="{{ route('admin.customers.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i> Customers
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-chart-bar"></i> Reports
        </div>

        <a href="{{ route('admin.reports.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Reports
        </a>

        <div class="sidebar-divider">
            <i class="fas fa-cog"></i> Settings
        </div>

        <a href="{{ route('admin.users.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> Users
        </a>

        <a href="{{ route('admin.settings.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i> Settings
        </a>

        <hr class="my-2 mx-3 border-secondary">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="list-group-item list-group-item-action text-danger border-0">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</nav>