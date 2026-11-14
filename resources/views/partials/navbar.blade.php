@php($authUser = auth()->user())
<nav class="app-header navbar navbar-expand bg-body" aria-label="Top navigation">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-5" aria-hidden="true"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userMenu"
                   role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu for {{ $authUser->name }}">
                    <i class="bi bi-person-circle fs-5" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline">{{ $authUser->name }}</span>
                    <x-role-badge :user="$authUser" class="d-none d-md-inline-flex" />
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenu">
                    <li class="dropdown-header small">{{ $authUser->email }}</li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2" aria-hidden="true"></i>My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
