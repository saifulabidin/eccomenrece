<div class="filament-topbar-widget">
    <div class="dropdown">
        <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            @if($adminUser && $adminUser['avatar'])
                <img src="{{ $adminUser['avatar'] }}"
                     alt="{{ $adminUser['name'] }}"
                     class="rounded-circle me-2"
                     style="width: 32px; height: 32px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                     style="width: 32px; height: 32px;">
                    <i class="bi bi-person-fill text-white"></i>
                </div>
            @endif
            <span class="text-dark">{{ $adminUser['name'] ?? $user->name }}</span>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <div class="dropdown-header">
                    <strong>{{ $adminUser['name'] ?? $user->name }}</strong>
                    <div class="small text-muted">{{ $adminUser['email'] ?? $user->email }}</div>
                </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('filament.admin.auth.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .filament-topbar-widget {
        display: flex;
        align-items: center;
    }

    .dropdown-toggle::after {
        display: none;
    }

    .dropdown-menu {
        min-width: 200px;
    }

    .dropdown-header {
        padding: 0.5rem 1rem;
    }
</style>