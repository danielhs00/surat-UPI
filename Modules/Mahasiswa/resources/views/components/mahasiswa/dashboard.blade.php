@extends('mahasiswa::components.layouts.mantis')
@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h4 class="mb-3">Mulai dokumen baru</h4>
        <div class="row">
            @foreach ($templates as $t)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-bold">{{ $t->name }}</div>
                            <div class="text-muted small mb-3">{{ $t->description }}</div>

                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('mahasiswa.templates.download', $t->id) }}">
                                    Download DOCX
                                </a>

                                <form method="POST" action="{{ route('mahasiswa.documents.fromTemplate', $t->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary" type="submit">Buat Draft</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-4">

        <h4 class="mb-3">Dokumen terbaru (terakhir diubah)</h4>
        <form method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">

                        <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>
                            Semua
                        </option>

                        <option value="approved_wadek" {{ ($status ?? '') === 'approved_wadek' ? 'selected' : '' }}>
                            Disetujui Wadek
                        </option>

                        <option value="verified_operator" {{ ($status ?? '') === 'verified_operator' ? 'selected' : '' }}>
                            Menunggu Wadek
                        </option>

                        <option value="rejected_wadek" {{ ($status ?? '') === 'rejected_wadek' ? 'selected' : '' }}>
                            Ditolak
                        </option>

                        <option value="converted" {{ ($status ?? '') === 'converted' ? 'selected' : '' }}>
                            Sudah Convert
                        </option>

                        <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>
                            Gagal Convert
                        </option>

                    </select>
                </div>

                <div class="col-auto">
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @forelse($recentDocs as $d)
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="fw-bold">{{ $d->title ?? 'Tanpa Judul' }}</div>
                            <div class="text-muted small">Status: {{ $d->status }}</div>
                            <div class="text-muted small mb-3">Update: {{ $d->updated_at->format('d M Y H:i') }}</div>

                            @if ($d->status === 'failed')
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

                                @if ($d->pdf_path)
                                    <a class="btn btn-sm btn-success w-100 mt-2"
                                        href="{{ route('mahasiswa.documents.downloadPdf', $d->id) }}">
                                        Download PDF
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada dokumen.</p>
            @endforelse
        </div>

        <div class="text-muted small mb-2">
            Status:
            @switch($d->status)
                @case('approved_wadek')
                    <span class="badge bg-success">Disetujui Wadek</span>
                @break

                @case('rejected_wadek')
                    <span class="badge bg-danger">Ditolak</span>
                @break

                @case('verified_operator')
                    <span class="badge bg-warning">Menunggu Wadek</span>
                @break

                @default
                    <span class="badge bg-secondary">
                        {{ ucfirst($d->status) }}
                    </span>
            @endswitch
        </div>

        @if (!empty($d->nomor_surat))
            <div class="small text-success">
                Nomor Surat: {{ $d->nomor_surat }}
            </div>
        @endif

        @if ($d->status === 'approved_wadek' && !empty($d->pdf_path))
            <a class="btn btn-sm btn-success w-100 mt-2" href="{{ route('mahasiswa.documents.downloadPdf', $d->id) }}">
                Download Surat Final (PDF)
            </a>
        @endif

    </div>
@endsection
