@extends('layouts.mantis')

@section('title', 'Dashboard Admin')
@include('components.mantis.header', ['role' => 'admin'])

@section('content')
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Dashboard Admin</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Stat cards -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Mahasiswa</h6>
                        <h4 class="mb-0">{{ $jumlah_mahasiswa ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Operator</h6>
                        <h4 class="mb-0">{{ $jumlah_operator ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Table Operator -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Monitoring Akun Operator</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalTambahOperator">
                            + Tambah Operator
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:60px">No</th>
                                        <th>Nama Operator</th>
                                        <th>Fakultas</th>
                                        <th>Prodi</th>
                                        <th style="width:120px">Status</th>
                                        <th style="width:190px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($operators ?? [] as $i => $op)
                                        @php $aktif = (bool) ($op->is_active ?? true); @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $op->user->name ?? '-' }}</div>
                                                <div class="text-muted small">{{ $op->user->email ?? '' }}</div>
                                            </td>
                                            <td>{{ $op->fakultas->nama_fakultas ?? '-' }}</td>
                                            <td>{{ $op->prodi->nama_prodi ?? '-' }}</td>
                                            <td>
                                                @if ($aktif)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="d-flex gap-2">
                                                <a href="{{ route('admin.operator.edit', $op->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('admin.operator.toggle', $op->id) }}" method="POST"
                                                    onsubmit="return confirm('Ubah status operator ini?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-sm btn-outline-dark" type="submit">
                                                        {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada data operator.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Operator -->
    <div class="modal fade" id="modalTambahOperator" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Operator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.operator.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fakultas</label>
                            <select name="fakultas_id" id="fakultasSelect"
                                class="form-select @error('fakultas_id') is-invalid @enderror" required>
                                <option value="">-- pilih fakultas --</option>
                                @foreach ($fakultas as $f)
                                    <option value="{{ $f->id }}"
                                        {{ old('fakultas_id') == $f->id ? 'selected' : '' }}>
                                        {{ $f->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <select name="prodi_id" id="prodiSelect"
                                class="form-select @error('prodi_id') is-invalid @enderror" required>
                                <option value="">-- pilih prodi --</option>
                            </select>
                            @error('prodi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Operator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Auto open modal jika validasi error -->
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('modalTambahOperator'));
                modal.show();
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fakultasSelect = document.getElementById('fakultasSelect');
            const prodiSelect = document.getElementById('prodiSelect');

            async function loadProdi(fakultasId, selectedProdiId = null) {
                prodiSelect.innerHTML = `<option value="">-- Memuat data... --</option>`;
                prodiSelect.disabled = true;

                if (!fakultasId) {
                    prodiSelect.innerHTML = `<option value="">-- pilih prodi --</option>`;
                    prodiSelect.disabled = false;
                    return;
                }

                try {
                    // ✅ ini route yang harus kamu sediakan di web.php
                    const url = `/admin/prodi/by-fakultas/${fakultasId}`;

                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const ct = res.headers.get('content-type') || '';
                    if (!res.ok || !ct.includes('application/json')) {
                        const text = await res.text();
                        console.error('Bukan JSON. Response awal:', text.slice(0, 200));
                        prodiSelect.innerHTML = `<option value="">-- Error format data --</option>`;
                        return;
                    }

                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        prodiSelect.innerHTML = `<option value="">-- Tidak ada prodi --</option>`;
                        return;
                    }

                    prodiSelect.innerHTML = `<option value="">-- pilih prodi --</option>` +
                        data.map(p => `<option value="${p.id}">${p.nama_prodi}</option>`).join('');

                    if (selectedProdiId) prodiSelect.value = selectedProdiId;

                } catch (err) {
                    console.error('Fetch prodi error:', err);
                    prodiSelect.innerHTML = `<option value="">-- Gagal memuat data --</option>`;
                } finally {
                    prodiSelect.disabled = false;
                }
            }

            fakultasSelect.addEventListener('change', (e) => {
                loadProdi(e.target.value);
            });

            // kalau habis validasi error dan ada old()
            @if (old('fakultas_id'))
                loadProdi("{{ old('fakultas_id') }}", "{{ old('prodi_id') }}");
            @endif
        });
    </script>
@endsection
