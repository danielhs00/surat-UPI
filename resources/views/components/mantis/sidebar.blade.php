@php
    $role = auth()->user()->role ?? null;

    $dashboardRoute = match ($role) {
        'mahasiswa' => route('mahasiswa.dashboard'),
        'operator'  => route('operator.dashboard'),
        'wadek'     => route('wadek.dashboard'),
        'admin'     => route('admin.dashboard'),
        default     => route('dashboard'),
    };

    $isActive = function (string $name) {
        return request()->routeIs($name) ? 'active' : '';
    };
@endphp

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ $dashboardRoute }}" class="b-brand text-primary">
                <img src="{{ asset('dist/assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">

                {{-- Dashboard --}}
                <li class="pc-item {{ $isActive($role . '.dashboard') }}">
                    <a href="{{ $dashboardRoute }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                {{-- Mahasiswa --}}
                @if ($role === 'mahasiswa')
                    <li class="pc-item pc-caption">
                        <label>Mahasiswa</label>
                        <i class="ti ti-user"></i>
                    </li>

                    <li class="pc-item {{ $isActive('mahasiswa.dashboard') }}">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                            <span class="pc-mtext">Dokumen Saya</span>
                        </a>
                    </li>
                @endif

                {{-- Operator --}}
                @if ($role === 'operator')
                    <li class="pc-item pc-caption">
                        <label>Operator</label>
                        <i class="ti ti-settings"></i>
                    </li>

                    <li class="pc-item {{ $isActive('operator.dashboard') }}">
                        <a href="{{ route('operator.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-checkup-list"></i></span>
                            <span class="pc-mtext">Verifikasi Dokumen</span>
                        </a>
                    </li>
                @endif

                {{-- Wadek --}}
                @if ($role === 'wadek')
                    <li class="pc-item pc-caption">
                        <label>Wadek</label>
                        <i class="ti ti-signature"></i>
                    </li>

                    <li class="pc-item {{ $isActive('wadek.dashboard') }}">
                        <a href="{{ route('wadek.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-signature"></i></span>
                            <span class="pc-mtext">Persetujuan Surat</span>
                        </a>
                    </li>
                @endif

                {{-- Admin --}}
                @if ($role === 'admin')
                    <li class="pc-item pc-caption">
                        <label>Admin</label>
                        <i class="ti ti-shield"></i>
                    </li>

                    <li class="pc-item {{ $isActive('admin.dashboard') }}">
                        <a href="{{ route('admin.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-layout-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard Admin</span>
                        </a>
                    </li>

                    {{-- route ini kamu punya: admin.wadek --}}
                    <li class="pc-item {{ $isActive('admin.wadek') }}">
                        <a href="{{ route('admin.wadek') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Kelola Wadek</span>
                        </a>
                    </li>
                @endif

                {{-- Akun --}}
                <li class="pc-item pc-caption">
                    <label>Akun</label>
                    <i class="ti ti-lock"></i>
                </li>

                <li class="pc-item">
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="pc-link border-0 bg-transparent w-100 text-start">
                            <span class="pc-micon"><i class="ti ti-logout"></i></span>
                            <span class="pc-mtext">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>