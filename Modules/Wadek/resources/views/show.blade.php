@extends('layouts.mantis')

@section('title', 'Detail Dokumen Wadek')

@section('content')
<div class="row">
  <div class="col-12">

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <b>Ada error:</b>
        <ul class="mb-0">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-0">Detail Dokumen</h4>
          <small class="text-muted">ID: {{ $document->id }}</small>
        </div>
        <a href="{{ route('wadek.dashboard') }}" class="btn btn-light btn-sm">Kembali</a>
      </div>

      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border rounded p-3">
              <div class="fw-bold mb-2">Mahasiswa</div>
              <div><b>Nama:</b> {{ $document->user->name ?? 'N/A' }}</div>
              <div><b>Email:</b> {{ $document->user->email ?? 'N/A' }}</div>
              <div><b>NIM:</b> {{ $document->user->mahasiswa->nim ?? 'N/A' }}</div>
              <div><b>Fakultas:</b> {{ $document->user->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3">
              <div class="fw-bold mb-2">Dokumen</div>
              <div><b>Template:</b> {{ $document->template->nama_template ?? 'N/A' }}</div>
              <div><b>Jenis Surat:</b> {{ $document->template->jenis_surat ?? 'N/A' }}</div>
              <div><b>Status:</b> <span class="badge bg-secondary">{{ $document->status }}</span></div>
              <div><b>Nomor Surat:</b> {{ $document->nomor_surat ?? '-' }}</div>
            </div>
          </div>
        </div>

        <hr>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <a class="btn btn-outline-success btn-sm"
             href="{{ route('wadek.documents.pdf', $document->id) }}"
             target="_blank" rel="noopener">
            Lihat PDF
          </a>
        </div>

        <div class="row g-3">
          {{-- Upload TTD --}}
          <div class="col-md-6">
            <div class="border rounded p-3">
              <div class="fw-bold mb-2">TTD Digital Wadek</div>

              @if (!empty($wdk->ttd_path))
                <div class="text-success mb-2">
                  TTD sudah ada ✅ ({{ !empty($wdk->ttd_uploaded_at) ? \Carbon\Carbon::parse($wdk->ttd_uploaded_at)->format('d M Y H:i') : '' }})
                </div>
              @else
                <div class="text-muted mb-2">TTD belum diupload.</div>
              @endif

              <form action="{{ route('wadek.signature.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="ttd" class="form-control mb-2" required>
                <button class="btn btn-primary btn-sm">Upload TTD</button>
              </form>

              <small class="text-muted d-block mt-2">
                Format: PNG/JPG. Disarankan PNG transparan.
              </small>
            </div>
          </div>

          {{-- Form nomor surat + sign --}}
          <div class="col-md-6">
            <div class="border rounded p-3">
              <div class="fw-bold mb-2">Nomor Surat & Keputusan</div>

              <form action="{{ route('wadek.documents.sign', $document->id) }}" method="POST">
                @csrf
                @method('PUT')

                <label class="form-label">Nomor Surat</label>
                <input type="text" name="nomor_surat"
                       class="form-control mb-2"
                       value="{{ old('nomor_surat', $document->nomor_surat ?? '') }}"
                       placeholder="Contoh: 123/UPI/FT/II/2026" required>

                <label class="form-label">Catatan Wadek (opsional)</label>
                <textarea name="catatan_wadek" rows="3" class="form-control mb-3"
                          placeholder="Catatan untuk operator/mahasiswa...">{{ old('catatan_wadek', $document->catatan_wadek ?? '') }}</textarea>

                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Yakin setujui + tanda tangan dokumen ini?')">
                  Setujui & TTD
                </button>
              </form>

              <hr>

              <form action="{{ route('wadek.documents.reject', $document->id) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="form-label">Catatan Penolakan (wajib kalau ditolak)</label>
                <textarea name="catatan_wadek" rows="3" class="form-control mb-2"
                          placeholder="Alasan ditolak / revisi yang diminta..." required>{{ old('catatan_wadek') }}</textarea>
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Yakin tolak dokumen ini?')">
                  Tolak
                </button>
              </form>

            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection