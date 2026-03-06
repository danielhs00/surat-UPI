@extends('layouts.mantis')

@section('title', 'Edit Pengajuan')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Edit Pengajuan</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            {{-- Info Pengajuan --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Info Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><b>NIM:</b> {{ $pengajuan->user->mahasiswa->nim ?? 'N/A' }}</p>
                        <p class="mb-1"><b>Nama:</b> {{ $pengajuan->user->name ?? 'N/A' }}</p>
                        <p class="mb-1"><b>Template:</b> {{ $pengajuan->template->nama_template ?? 'N/A' }}</p>
                        <p class="mb-1"><b>Status:</b> <span
                                class="badge bg-secondary">{{ $pengajuan->status ?? '-' }}</span></p>
                        <p class="mb-0"><b>Dibuat:</b> {{ optional($pengajuan->created_at)->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Aksi</h6>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2">

                        {{-- Lihat PDF --}}
                        <a class="btn btn-outline-primary" href="{{ route('operator.pengajuan.pdf', $pengajuan->id) }}"
                            target="_blank">
                            Lihat PDF
                        </a>

                        {{-- Tandai Diproses Offline --}}
                        <form action="{{ route('operator.pengajuan.mark_offline', $pengajuan->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-warning"
                                onclick="return confirm('Tandai pengajuan ini sebagai diproses offline?')">
                                Tandai Diproses Offline
                            </button>
                        </form>

                        <div class="w-100 mt-2 small text-muted">
                            PDF: {{ $pengajuan->pdf_path ? 'Ada' : 'Belum ada' }} |
                            PDF Final: {{ $pengajuan->signed_pdf_path ? 'Ada' : 'Belum ada' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Simpan + Upload Final (opsional) --}}
            {{-- Form Simpan + Upload Final (opsional) --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Simpan Perubahan / Upload Surat Final</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('operator.pengajuan.complete', $pengajuan->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        @php
                                            $statuses = [
                                                'draft',
                                                'mengupload',
                                                'converting',
                                                'converted',
                                                'gagal',
                                                'submitted',
                                                'processing_offline',
                                                'completed',
                                                'rejected',
                                            ];
                                        @endphp
                                        <select name="status" class="form-control">
                                            @foreach ($statuses as $st)
                                                <option value="{{ $st }}" @selected(old('status', $pengajuan->status) === $st)>
                                                    {{ $st }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            Jika kamu upload PDF final dan isi nomor surat, status akan otomatis menjadi
                                            <b>completed</b>.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan Operator</label>
                                        <textarea name="catatan_operator" class="form-control" rows="4">{{ old('catatan_operator', $pengajuan->catatan_operator) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <b>Catatan:</b><br>
                                        Upload PDF final bersifat opsional.<br>
                                        Namun jika kamu upload PDF final, maka:
                                        <ul class="mb-0 mt-2">
                                            <li>Nomor surat wajib diisi</li>
                                            <li>Status otomatis menjadi <b>completed</b></li>
                                        </ul>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nomor Surat</label>
                                        <input type="text" name="nomor_surat" class="form-control"
                                            value="{{ old('nomor_surat', $pengajuan->nomor_surat) }}"
                                            placeholder="Contoh: 123/UN40/KM.00.00/2026">
                                        <div class="form-text">
                                            Wajib diisi jika upload PDF final.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">PDF Final (sudah TTD)</label>
                                        <input type="file" name="signed_pdf" class="form-control"
                                            accept="application/pdf">
                                        <div class="form-text">
                                            Format PDF, maksimal 5MB.
                                        </div>
                                    </div>

                                    @if ($pengajuan->signed_pdf_path)
                                        <div class="mb-3">
                                            <span class="badge bg-success">PDF Final sudah tersedia</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Simpan perubahan? Jika upload PDF final dan nomor surat diisi, status akan otomatis menjadi completed.')">
                                Simpan Perubahan
                            </button>

                            <a href="{{ route('operator.pengajuan') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pdfInput = document.querySelector('input[name="signed_pdf"]');
            const nomorInput = document.querySelector('input[name="nomor_surat"]');

            if (pdfInput && nomorInput) {
                pdfInput.addEventListener('change', function() {
                    if (pdfInput.files.length > 0) {
                        nomorInput.setAttribute('required', 'required');
                    } else {
                        nomorInput.removeAttribute('required');
                    }
                });
            }
        });
    </script>
@endsection
