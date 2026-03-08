@extends('layouts.mantis')

@section('title', 'Daftar Template Surat')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
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
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Search Bar --}}
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="myInput" class="form-control" placeholder="Cari template surat...">
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table id="myTable" class="table table-hover">
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
                                <tr class="template-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $template->nama_template }}</div>
                                        @if ($template->deskripsi)
                                            <small class="text-muted">
                                                {{ \Illuminate\Support\Str::limit($template->deskripsi, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary">
                                            {{ $template->jenis_surat ?? '-' }}
                                        </span>
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
                                            @php
                                                $fileSize = null;
                                                $filePath = storage_path('app/public/' . $template->file_docx_path);

                                                if (file_exists($filePath)) {
                                                    $fileSize = number_format(filesize($filePath) / 1024, 2) . ' KB';
                                                }
                                            @endphp

                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-file-text text-primary me-2" style="font-size: 1.2rem;"></i>
                                                <div>
                                                    <a href="{{ Storage::url($template->file_docx_path) }}"
                                                        target="_blank"
                                                        class="text-primary text-hover-primary fw-bold">
                                                        {{ basename($template->file_docx_path) }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $fileSize ?? '-' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="ti ti-file-off me-1"></i> File belum diupload
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ optional($template->created_at)->format('d M Y') ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('operator.template.edit', $template->id) }}"
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="tooltip"
                                                title="Edit Template">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $template->id }}"
                                                title="Hapus Template">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Modal Delete --}}
                                        <div class="modal fade"
                                            id="deleteModal{{ $template->id }}"
                                            tabindex="-1"
                                            aria-labelledby="deleteModalLabel{{ $template->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $template->id }}">
                                                            Konfirmasi Hapus
                                                        </h5>
                                                        <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>Apakah Anda yakin ingin menghapus template berikut?</p>
                                                        <p class="fw-bold mb-0">
                                                            Nama Template: {{ $template->nama_template }}
                                                        </p>
                                                        <p class="text-muted">
                                                            Jenis: {{ $template->jenis_surat ?? '-' }}
                                                        </p>
                                                        <hr>
                                                        <p class="text-danger mb-0">
                                                            <i class="ti ti-alert-triangle me-1"></i>
                                                            Semua data yang terkait dengan template ini akan dihapus permanen!
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                            <i class="ti ti-x me-1"></i> Batal
                                                        </button>

                                                        <form action="{{ route('operator.template.destroy', $template->id) }}"
                                                            method="POST"
                                                            class="d-inline">
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
                                <tr id="emptyRow">
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

                            <tr id="noDataRow" style="display: none;">
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="ti ti-search-off me-1"></i> Data tidak ditemukan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const input = document.getElementById('myInput');
        const rows = document.querySelectorAll('#myTable tbody tr.template-row');
        const noDataRow = document.getElementById('noDataRow');
        const emptyRow = document.getElementById('emptyRow');

        if (input) {
            input.addEventListener('keyup', function () {
                const filter = this.value.toUpperCase();
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const rowText = row.textContent || row.innerText;

                    if (rowText.toUpperCase().indexOf(filter) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyRow) {
                    emptyRow.style.display = filter === '' ? '' : 'none';
                }

                if (noDataRow && rows.length > 0) {
                    noDataRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            });
        }
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

    .table > :not(caption) > * > * {
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