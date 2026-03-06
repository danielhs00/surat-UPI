@php
    $role = auth()->user()->role ?? null;

    $dashboardRoute = match ($role) {
        'mahasiswa' => route('mahasiswa.dashboard'),
        'operator' => route('operator.dashboard'),
        'wadek' => route('wadek.dashboard'),
        'admin' => route('admin.dashboard'),
        default => route('dashboard'),
    };

    $isActive = function (string $name) {
        return request()->routeIs($name) ? 'active' : '';
    };
@endphp

<div>
    <div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">

                    {{-- WADEK --}}
                    @if ($role == 'wadek')
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg" alt="user-image"
                                class="user-avtar">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg"
                                            alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <span>{{ auth()->user()->fakultas->nama_fakultas ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                     {{-- Operator --}}
                    @if ($role == 'operator')
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg" alt="user-image"
                                class="user-avtar">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg"
                                            alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <span>{{ auth()->user()->fakultas->nama_fakultas ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                     {{-- Mahasiswa --}}
                    @if ($role == 'mahasiswa')
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg" alt="user-image"
                                class="user-avtar">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg"
                                            alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <span>{{ auth()->user()->fakultas->nama_fakultas ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif

                    @if ($role == 'admin')
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg" alt="user-image"
                                class="user-avtar">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('dist') }}/assets/images/user/avatar-2.jpg"
                                            alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <span>{{ auth()->user()->fakultas->nama_fakultas ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </header>
</div>
<script src="{{ asset('dist/assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('dist/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('dist/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('dist/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/assets/js/pcoded.js') }}"></script>
