@php
    $role = auth()->user()->role ?? null;

    $dashboardRoute = match ($role) {
        'mahasiswa' => route('mahasiswa.dashboard'),
        'operator'  => route('operator.dashboard'),
        'wadek'     => route('wadek.dashboard'),
        'admin'     => route('admin.dashboard'),
        default     => route('dashboard'),
    };

    // judul halaman dari @section('title', '...')
    $title = trim($__env->yieldContent('title')) ?: 'Home';

    // nama role untuk label breadcrumb
    $roleLabel = match ($role) {
        'mahasiswa' => 'Mahasiswa',
        'operator'  => 'Operator',
        'wadek'     => 'Wadek',
        'admin'     => 'Admin',
        default     => 'Home',
    };
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $title }}</h5>
                </div>

                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ $dashboardRoute }}">{{ $roleLabel }}</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>