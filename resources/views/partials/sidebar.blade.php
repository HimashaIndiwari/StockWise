@php($authUser = auth()->user())
<aside class="app-sidebar shadow" data-bs-theme="dark" aria-label="Main navigation">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <i class="bi bi-box-seam brand-icon me-2" aria-hidden="true"></i>
            <span class="brand-text">StockWise</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" role="menu">
                @if ($authUser->isAdmin())
                    <x-nav-item route="admin.dashboard" active="admin.dashboard" icon="speedometer2">Dashboard</x-nav-item>

                    <li class="nav-header">Inventory</li>
                    <x-nav-item route="admin.products.index" active="admin.products.*" icon="box-seam">Products</x-nav-item>
                    <x-nav-item route="admin.categories.index" active="admin.categories.*" icon="tags">Categories</x-nav-item>
                    <x-nav-item route="admin.suppliers.index" active="admin.suppliers.*" icon="truck">Suppliers</x-nav-item>
                    <x-nav-item route="admin.stock-movements.index" active="admin.stock-movements.*" icon="arrow-left-right">Stock Movements</x-nav-item>

                    <li class="nav-header">Administration</li>
                    <x-nav-item route="admin.users.index" active="admin.users.*" icon="people">User Management</x-nav-item>
                    <x-nav-item route="admin.reports.index" active="admin.reports.*" icon="bar-chart-line">Reports</x-nav-item>
                @else
                    <x-nav-item route="staff.dashboard" active="staff.dashboard" icon="speedometer2">Dashboard</x-nav-item>

                    <li class="nav-header">Inventory</li>
                    <x-nav-item route="staff.products.index" active="staff.products.*" icon="box-seam">My Products</x-nav-item>
                    <x-nav-item route="staff.stock-movements.index" active="staff.stock-movements.*" icon="arrow-left-right">Stock Movements</x-nav-item>
                @endif

                <li class="nav-header">Account</li>
                <x-nav-item route="profile.edit" active="profile.*" icon="person">My Profile</x-nav-item>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <i class="nav-icon bi bi-box-arrow-right" aria-hidden="true"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
