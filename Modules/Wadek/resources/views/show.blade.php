@extends('layouts.mantis')

@section('title', 'Detail Pengajuan')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Detail Pengajuan</h4>
                        <small class="text-muted">ID Dokumen: {{ $document->id }}</small>
                    </div>

                    <a href="{{ route('wadek.dashboard') }}" class="btn btn-light btn-sm">Kembali</a>
                </div>

                <div class="card-body">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold mb-2">Data Mahasiswa</div>
                                <div><b>Nama:</b> {{ $document->user->name ?? 'N/A' }}</div>
                                <div><b>Email:</b> {{ $document->user->email ?? 'N/A' }}</div>
                                <div><b>NIM:</b> {{ $document->user->mahasiswa->nim ?? 'N/A' }}</div>
                                <div><b>Fakultas:</b> {{ $document->user->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold mb-2">Dokumen</div>
                                <div><b>Template:</b> {{ $document->template->nama_template ?? 'N/A' }}</div>
                                <div><b>Jenis Surat:</b> {{ $document->template->jenis_surat ?? 'N/A' }}</div>
                                <div><b>Status:</b> <span class="badge bg-secondary">{{ $document->status }}</span></div>
                                <div><b>Dibuat:</b> {{ optional($document->created_at)->format('d M Y H:i') }}</div>
                                <div><b>Diupdate:</b> {{ optional($document->updated_at)->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3">
                        <div class="fw-bold mb-2">File</div>

                        <div class="d-flex flex-wrap gap-2">
                            @if (!empty($document->docx_path))
                                <a class="btn btn-outline-primary btn-sm" href="{{ Storage::url($document->docx_path) }}"
                                    target="_blank" rel="noopener">
                                    Lihat DOCX
                                </a>
                            @else
                                <span class="text-muted">DOCX belum ada.</span>
                            @endif

                            @if (!empty($document->pdf_path))
                                <a class="btn btn-outline-success btn-sm"
                                    href="{{ route('wadek.documents.pdf', $document->id) }}" target="_blank"
                                    rel="noopener">
                                    Lihat PDF
                                </a>
                            @else
                                <span class="text-muted">PDF belum ada.</span>
                            @endif
                        </div>

                        @if (!empty($document->convert_error))
                            <div class="mt-3">
                                <div class="fw-bold text-danger">Convert Error</div>
                                <pre class="mb-0" style="white-space: pre-wrap;">{{ $document->convert_error }}</pre>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
