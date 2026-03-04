@extends('layouts.mantis')

@section('title', 'Daftar Template Surat')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Manajemen Template Surat</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Template Surat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Daftar Template Surat</h5>
                            <a href="{{ route('operator.template.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Tambah Template
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Alert Success --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ti ti-check me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Search Bar --}}
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <form class="d-flex" action="{{ route('operator.template.index') }}" method="GET">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
                                        <input type="search" name="search" class="form-control"
                                            placeholder="Cari template surat..." value="{{ request('search') }}">
                                        @if (request('search'))
                                            <a href="{{ route('operator.template.index') }}"
                                                class="btn btn-outline-secondary" type="button">
                                                <i class="ti ti-x"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-hover" id="pengajuanTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Template</th>
                                        <th width="15%">Jenis Surat</th>
                                        <th width="10%">Status</th>
                                        <th width="25%">File Template</th>
                                        <th width="15%">Tanggal Upload</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates as $index => $template)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $template->nama_template }}</div>
                                                @if ($template->deskripsi)
                                                    <small
                                                        class="text-muted">{{ Str::limit($template->deskripsi, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-light-primary text-primary">{{ $template->jenis_surat ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if ($template->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($template->file_docx_path))
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-file-text text-primary me-2"
                                                            style="font-size: 1.2rem;"></i>
                                                        <div>
                                                            <a href="{{ Storage::url($template->file_docx_path) }}"
                                                                target="_blank"
                                                                class="text-primary text-hover-primary fw-bold">
                                                                {{ basename($template->file_docx_path) }}
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{-- Format ukuran file jika ada --}}
                                                                @php
                                                                    $filePath = storage_path(
                                                                        'app/public/' . $template->file_docx_path,
                                                                    );
                                                                    if (file_exists($filePath)) {
                                                                        $size = filesize($filePath);
                                                                        echo number_format($size / 1024, 2) . ' KB';
                                                                    }
                                                                @endphp
                                                            </small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="ti ti-file-off me-1"></i> File belum diupload
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $template->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $template->created_at->format('H:i') }}
                                                    WIB</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('operator.template.edit', $template->id) }}"
                                                        class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                        title="Edit Template">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $template->id }}"
                                                        title="Hapus Template">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>

                                                {{-- Modal Delete --}}
                                                <div class="modal fade" id="deleteModal{{ $template->id }}" tabindex="-1"
                                                    aria-labelledby="deleteModalLabel{{ $template->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteModalLabel{{ $template->id }}">
                                                                    Konfirmasi Hapus
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p>Apakah Anda yakin ingin menghapus template berikut?</p>
                                                                <p class="fw-bold mb-0">Nama Template:
                                                                    {{ $template->nama_template }}</p>
                                                                <p class="text-muted">Jenis: {{ $template->jenis_surat }}
                                                                </p>
                                                                <hr>
                                                                <p class="text-danger mb-0">
                                                                    <i class="ti ti-alert-triangle me-1"></i>
                                                                    Semua data yang terkait dengan template ini akan dihapus
                                                                    permanen!
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
                                                                    <i class="ti ti-x me-1"></i> Batal
                                                                </button>
                                                                <form
                                                                    action="{{ route('operator.template.destroy', $template->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="ti ti-trash me-1"></i> Ya, Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="ti ti-file-text" style="font-size: 3rem; opacity: 0.5;"></i>
                                                    <h6 class="mt-3">Belum ada template surat</h6>
                                                    <p class="text-muted mb-3">Silakan tambah template surat baru</p>
                                                    <a href="{{ route('operator.template.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="ti ti-plus me-1"></i> Tambah Template
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if (method_exists($templates, 'links'))
                            <div class="mt-3">
                                {{ $templates->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Search functionality with debounce
        let searchTimeout;
        document.querySelector('input[name="search"]')?.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    </script>
@endpush

@push('styles')
    <style>
        .empty-state {
            text-align: center;
            padding: 2rem;
        }

        .empty-state i {
            color: #6c757d;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .bg-light-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .text-hover-primary:hover {
            color: #0d6efd !important;
            text-decoration: underline;
        }
    </style>
@endpush
