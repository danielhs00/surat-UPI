@php
    $role = auth()->user()->role ?? null;

    $dashboardRoute = match ($role) {
        'mahasiswa' => route('mahasiswa.dashboard'),
        'operator' => route('operator.dashboard'),
        'admin' => route('admin.dashboard'),
        default => route('dashboard'),
    };

    $isActive = function (string $name) {
        return request()->routeIs($name) ? 'active' : '';
    };
@endphp

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ $dashboardRoute }}" class="b-brand text-primary">
                <img src="{{ asset('assets/images/logo_surat.png') }}" class="img-fluid logo-lg" alt="logo"
                    width="50px">
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">

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

                    <li class="pc-item {{ request()->routeIs('mahasiswa.surat.selesai') ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.surat.selesai') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-check"></i></span>
                            <span class="pc-mtext">Surat Selesai</span>
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
                        <a href="{{ route('operator.template.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-description"></i></span>
                            <span class="pc-mtext">Template Surat</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('operator.pengajuan') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-headset"></i></span>
                            <span class="pc-mtext">Pengajuan Masuk</span>
                        </a>
                    </li>
                    <li class="pc-item {{ request()->routeIs('operator.pengajuan.hasil') ? 'active' : '' }}">
                        <a href="{{ route('operator.pengajuan.hasil') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-check"></i></span>
                            <span class="pc-mtext">Pengajuan Hasil</span>
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
                @endif

                {{-- Akun --}}
                <li class="pc-item pc-caption">
                    <label>Akun</label>
                    <i class="ti ti-lock"></i>
                </li>

                <li class="pc-item">
                    <form method="POST" id="logout-form" action="{{ route('logout') }}" class="m-0 p-0">
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
