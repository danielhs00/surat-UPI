@extends('layouts.mantis')

@section('title', 'Dashboard Mahasiswa')
@include('components.mantis.header', ['role' => 'mahasiswa'])

@section('content')
<div class="container">

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- =======================
        SECTION: TEMPLATE
    ======================== --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Mulai dokumen baru</h4>
    </div>

    <div class="row g-3">
        @forelse ($templates as $t)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-bold">{{ $t->nama_template }}</div>
                        <div class="text-muted small mt-1 mb-3">{{ $t->deskripsi ?? '-' }}</div>

                        <div class="mt-auto d-flex gap-2">
                            @if (!empty($t->file_docx_path))
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('mahasiswa.templates.download', $t->id) }}">
                                    Download DOCX
                                </a>
                            @elseif (!empty($t->google_docs_url))
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ $t->google_docs_url }}" target="_blank" rel="noopener">
                                    Buka Google Docs
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>Tidak ada file</button>
                            @endif

                            <form method="POST" action="{{ route('mahasiswa.documents.fromTemplate', $t->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary" type="submit">Buat Draft</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted mb-0">Belum ada template.</p>
            </div>
        @endforelse
    </div>

    <hr class="my-4">

    {{-- =======================
        SECTION: RECENT DOCS
    ======================== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h4 class="mb-0">Dokumen terbaru</h4>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Filter status --}}
            <form method="GET" class="d-inline">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="revisi" {{ ($status ?? '') === 'revisi' ? 'selected' : '' }}>Revisi</option>
                    <option value="uploaded" {{ ($status ?? '') === 'uploaded' ? 'selected' : '' }}>Telah di Upload</option>
                    <option value="converting" {{ ($status ?? '') === 'converting' ? 'selected' : '' }}>Converting</option>
                    <option value="converted" {{ ($status ?? '') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="processing_offline" {{ ($status ?? '') === 'processing_offline' ? 'selected' : '' }}>Diproses Offline</option>
                    <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Gagal Convert</option>
                </select>
            </form>

            {{-- Reset --}}
            <form action="{{ route('mahasiswa.documents.clearDashboard') }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Sembunyikan dokumen (kecuali yang selesai/ditolak)?')">
                    Reset Daftar
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($recentDocs as $d)
            @php
                $pdfPath = $d->signed_pdf_path ?: $d->pdf_path;
                $isFinal = ($d->status === 'completed');
                $isWaitingOffline = ($d->status === 'processing_offline');
                $canUpload = !$isFinal && !$isWaitingOffline; // upload tidak muncul saat menunggu offline & final
            @endphp

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">

                        <div class="fw-bold text-truncate" title="{{ $d->title ?? 'Tanpa Judul' }}">
                            {{ $d->title ?? 'Tanpa Judul' }}
                        </div>

                        {{-- Badge status --}}
                        <div class="mt-2 mb-2">
                            @switch($d->status)
                                @case('revisi')
                                    <span class="badge bg-warning text-dark">Revisi</span>
                                @break

                                @case('uploaded')
                                    <span class="badge bg-primary">Telah di Upload</span>
                                @break

                                @case('converting')
                                    <span class="badge bg-info text-dark">Converting</span>
                                @break

                                @case('converted')
                                    <span class="badge bg-info text-dark">Converted</span>
                                @break

                                @case('processing_offline')
                                    <span class="badge bg-warning text-dark">Diproses Offline</span>
                                @break

                                @case('completed')
                                    <span class="badge bg-success">Selesai (Final)</span>
                                @break

                                @case('rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @break

                                @case('failed')
                                    <span class="badge bg-danger">Gagal Convert</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">{{ $d->status }}</span>
                            @endswitch
                        </div>

                        <div class="text-muted small">
                            Terakhir update: {{ optional($d->updated_at)->format('d M Y H:i') }}
                        </div>

                        {{-- Nomor surat --}}
                        @if (!empty($d->nomor_surat))
                            <div class="small text-success mt-2">
                                Nomor Surat: <b>{{ $d->nomor_surat }}</b>
                            </div>
                        @endif

                        {{-- Alasan ditolak --}}
                        @if ($d->status === 'rejected' && !empty($d->catatan_operator))
                            <div class="alert alert-danger p-2 small mt-2 mb-0">
                                <b>Alasan:</b> {{ \Illuminate\Support\Str::limit($d->catatan_operator, 120) }}
                            </div>
                        @endif

                        {{-- Convert error --}}
                        @if ($d->status === 'failed' && !empty($d->convert_error))
                            <div class="alert alert-danger p-2 small mt-2 mb-0">
                                <b>Convert gagal:</b> {{ \Illuminate\Support\Str::limit($d->convert_error, 120) }}
                            </div>
                        @endif

                        <div class="mt-auto pt-3">

                            {{-- Upload DOCX --}}
                            @if ($canUpload)
                                <form method="POST" action="{{ route('mahasiswa.documents.uploadDocx', $d->id) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="docx" class="form-control form-control-sm mb-2" required>
                                    <button class="btn btn-sm btn-outline-dark w-100" type="submit">
                                        Upload DOCX (Convert ke PDF)
                                    </button>
                                </form>
                            @endif

                            {{-- Tombol PDF --}}
                            @if ($pdfPath)
                                <a class="btn btn-sm btn-primary w-100 mt-2"
                                   href="{{ route('mahasiswa.documents.pdf', $d->id) }}"
                                   target="_blank" rel="noopener">
                                    Lihat PDF{{ $d->signed_pdf_path ? ' Final' : '' }}
                                </a>

                                <a class="btn btn-sm btn-success w-100 mt-2"
                                   href="{{ route('mahasiswa.documents.downloadPdf', $d->id) }}">
                                    Download PDF{{ $d->signed_pdf_path ? ' Final' : '' }}
                                </a>
                            @else
                                <div class="text-muted small mt-2">Belum ada PDF</div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted mb-0">Belum ada dokumen.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection