@extends('layouts.mantis')

@section('content')
@section('title', 'Dashboard Mahasiswa')
<div class="container">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h4 class="mb-3">Mulai dokumen baru</h4>
    <div class="row">
        @forelse ($templates as $t)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-bold">{{ $t->nama_template }}</div>
                        <div class="text-muted small mb-3">{{ $t->deskripsi ?? '-' }}</div>

                        <div class="mt-auto d-flex gap-2">
                            @if (!empty($t->docx_path))
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('mahasiswa.templates.download', $t->id) }}">
                                    Download DOCX
                                </a>
                            @elseif (!empty($t->google_docs_url))
                                <a class="btn btn-sm btn-outline-primary" href="{{ $t->google_docs_url }}"
                                    target="_blank" rel="noopener">
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
            <p class="text-muted">Belum ada template.</p>
        @endforelse
    </div>

    <hr class="my-4">

    <h4 class="mb-3">Dokumen terbaru (terakhir diubah)</h4>

    {{-- filter status --}}
    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="approved_wadek" {{ ($status ?? '') === 'approved_wadek' ? 'selected' : '' }}>
                        Disetujui Wadek</option>
                    <option value="verified_operator" {{ ($status ?? '') === 'verified_operator' ? 'selected' : '' }}>
                        Menunggu Wadek</option>
                    <option value="rejected_wadek" {{ ($status ?? '') === 'rejected_wadek' ? 'selected' : '' }}>Ditolak
                    </option>
                    <option value="converted" {{ ($status ?? '') === 'converted' ? 'selected' : '' }}>Sudah Convert
                    </option>
                    <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Gagal Convert</option>
                </select>
            </div>

            <div class="col-auto">
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse($recentDocs as $d)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">

                        <div class="fw-bold">{{ $d->title ?? 'Tanpa Judul' }}</div>

                        {{-- badge status --}}
                        <div class="mb-2">
                            @switch($d->status)
                                @case('approved_wadek')
                                    <span class="badge bg-success">Disetujui Wadek</span>
                                @break

                                @case('rejected_wadek')
                                    <span class="badge bg-danger">Ditolak</span>
                                @break

                                @case('verified_operator')
                                    <span class="badge bg-warning text-dark">Menunggu Wadek</span>
                                @break

                                @case('converted')
                                    <span class="badge bg-info text-dark">Sudah Convert</span>
                                @break

                                @case('failed')
                                    <span class="badge bg-danger">Gagal Convert</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($d->status) }}</span>
                            @endswitch
                        </div>

                        <div class="text-muted small mb-2">
                            Update: {{ optional($d->updated_at)->format('d M Y H:i') }}
                        </div>

                        {{-- nomor surat kalau ada --}}
                        @if (!empty($d->nomor_surat))
                            <div class="small text-success mb-2">
                                Nomor Surat: {{ $d->nomor_surat }}
                            </div>
                        @endif

                        {{-- convert error --}}
                        @if ($d->status === 'failed' && !empty($d->convert_error))
                            <div class="alert alert-danger p-2 small">
                                Convert gagal: {{ \Illuminate\Support\Str::limit($d->convert_error, 120) }}
                            </div>
                        @endif

                        <div class="mt-auto">
                            {{-- upload docx --}}
                            <form method="POST" action="{{ route('mahasiswa.documents.uploadDocx', $d->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="docx" class="form-control form-control-sm mb-2" required>
                                <button class="btn btn-sm btn-outline-dark w-100" type="submit">
                                    Upload DOCX (Convert ke PDF)
                                </button>
                            </form>

                            {{-- Download PDF hanya jika status converted / approved_wadek --}}
                            @if (!empty($d->pdf_path) && in_array($d->status, ['converted', 'approved_wadek', 'signed', 'signed_by_wadek'], true))
                                <a class="btn btn-sm btn-success w-100 mt-2"
                                    href="{{ route('mahasiswa.documents.downloadPdf', $d->id) }}">
                                    Download PDF
                                </a>
                            @endif

                            @php
                                $pdfFinal = $d->signed_pdf_path ?: $d->pdf_path;
                            @endphp

                            @if (!empty($pdfFinal))
                                <a class="btn btn-sm {{ $d->signed_pdf_path ? 'btn-success' : 'btn-primary' }} w-100 mt-2"
                                    href="{{ route('mahasiswa.dokumen.pdf', $d->id) }}" target="_blank" rel="noopener">
                                    {{ $d->signed_pdf_path ? 'Lihat PDF TTD' : 'Lihat PDF' }}
                                </a>
                            @else
                                <span class="text-muted">Belum ada PDF</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @empty
                <p class="text-muted">Belum ada dokumen.</p>
            @endforelse
        </div>

    </div>
@endsection
