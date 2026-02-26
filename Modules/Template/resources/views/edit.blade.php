@extends('layouts.mantis')

@section('title', 'Edit Pengajuan')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Edit Pengajuan Mahasiswa</h4>
                        <small class="text-muted">ID Dokumen: {{ $pengajuan->id }}</small>
                    </div>

                    <a href="{{ route('operator.pengajuan') }}" class="btn btn-light btn-sm">
                        Kembali
                    </a>
                </div>

                <div class="card-body">

                    {{-- Alert --}}
                    @if (session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <div class="fw-bold mb-1">Ada error:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info ringkas --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold mb-2">Data Mahasiswa</div>
                                <div><b>Nama:</b> {{ $pengajuan->user->name ?? 'N/A' }}</div>
                                <div><b>Email:</b> {{ $pengajuan->user->email ?? 'N/A' }}</div>
                                <div><b>NIM:</b> {{ $pengajuan->user->mahasiswa->nim ?? 'N/A' }}</div>
                                <div><b>Fakultas:</b> {{ $pengajuan->user->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold mb-2">Data Template & Dokumen</div>
                                <div><b>Template:</b> {{ $pengajuan->template->nama_template ?? 'N/A' }}</div>
                                <div><b>Jenis Surat:</b> {{ $pengajuan->template->jenis_surat ?? 'N/A' }}</div>
                                <div><b>Status:</b> <span class="badge bg-secondary">{{ $pengajuan->status }}</span></div>
                                <div><b>Dibuat:</b> {{ optional($pengajuan->created_at)->format('d M Y H:i') }}</div>
                                <div><b>Diupdate:</b> {{ optional($pengajuan->updated_at)->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Link file --}}
                    <div class="border rounded p-3 mb-3">
                        <div class="fw-bold mb-2">File</div>

                        <div class="d-flex flex-wrap gap-2">
                            @if (!empty($pengajuan->docx_path))
                                <a class="btn btn-outline-primary btn-sm"
                                    href="{{ route('operator.pengajuan.docx', $pengajuan->id) }}" target="_blank"
                                    rel="noopener">
                                    Download DOCX
                                </a>
                            @else
                                <span class="text-muted">DOCX belum ada.</span>
                            @endif

                            @php
                                $pdfTampil = $pengajuan->signed_pdf_path ?: $pengajuan->pdf_path;
                            @endphp

                            @if (!empty($pdfTampil))
                                <a class="btn btn-outline-success btn-sm"
                                    href="{{ route('operator.pengajuan.pdf', $pengajuan->id) }}" target="_blank"
                                    rel="noopener">
                                    Lihat PDF
                                </a>
                            @else
                                <span class="text-muted">PDF belum ada.</span>
                            @endif

                            @if (!empty($pengajuan->convert_error))
                                <div class="mt-3">
                                    <div class="fw-bold text-danger">Convert Error</div>
                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ $pengajuan->convert_error }}</pre>
                                </div>
                            @endif
                        </div>

                        {{-- Form Update Status --}}
                        <form id="formUpdatePengajuan" action="{{ route('operator.pengajuan.update', $pengajuan->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-1 py-4 ">
                                <div class="col-md-6">
                                    <label class="form-label">Ubah Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        @php $current = old('status', $pengajuan->status); @endphp
                                        <option value="draft" {{ $current == 'draft' ? 'selected' : '' }}>draft</option>
                                        <option value="mengupload" {{ $current == 'mengupload' ? 'selected' : '' }}>Mengupload
                                        </option>
                                        <option value="converting" {{ $current == 'converting' ? 'selected' : '' }}>
                                            converting
                                        </option>
                                        <option value="converted" {{ $current == 'converted' ? 'selected' : '' }}>converted
                                        </option>
                                        <option value="gagal" {{ $current == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                        {{-- kalau kamu pakai status ini, tambahkan di controller validate() juga --}}
                                        <option value="kirim ke wadek" {{ $current == 'kirim ke wadek' ? 'selected' : '' }}>
                                            Kirim ke wadek</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pilih <b>gagal</b> kalau ada error convert.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Catatan Operator (opsional)</label>
                                    <textarea name="catatan_operator" rows="3" class="form-control @error('catatan_operator') is-invalid @enderror"
                                        placeholder="Contoh: Mohon revisi bagian ...">{{ old('catatan_operator', $pengajuan->catatan_operator ?? '') }}</textarea>
                                    @error('catatan_operator')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Kalau tabel <code>student_documents</code> belum ada kolom
                                        <code>catatan_operator</code>,
                                        hapus field ini dari blade & controller.
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('operator.pengajuan') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>

                        {{-- Tombol Kirim ke Wadek (form terpisah, tidak nested) --}}
                        <div class="d-flex justify-content-end mt-2">
                            @if ($pengajuan->status !== 'sent_to_wadek')
                                <form action="{{ route('operator.pengajuan.kirim_wadek', $pengajuan->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" class="btn btn-success"
                                    data-bs-toggle="modal" data-bs-target="#kirimWadekModal{{ $pengajuan->id }}">
                                    Kirim ke Wadek
                                </button>
                                </form>
                                <!-- Modal -->
<div class="modal fade" id="kirimWadekModal{{ $pengajuan->id }}" tabindex="-1" 
    aria-labelledby="kirimWadekLabel{{ $pengajuan->id }}" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="kirimWadekLabel{{ $pengajuan->id }}">
                    Konfirmasi Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                Apakah Anda yakin ingin mengirim pengajuan ini ke Wadek?
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <form action="{{ route('operator.pengajuan.kirim_wadek', $pengajuan->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">
                        Ya, Kirim
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
                            @else
                                <button class="btn btn-success" disabled>Sudah dikirim ke Wadek</button>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @endsection

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
