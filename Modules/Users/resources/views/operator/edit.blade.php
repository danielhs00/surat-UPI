@extends('layouts.mantis')

@section('title', 'Edit Operator')
@include('components.mantis.header', ['role' => 'admin'])

@section('content')
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Edit Operator</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="row">
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

            {{-- Form --}}
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('admin.operator.update', $operator->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" value="{{ old('name', $operator->user->name ?? '') }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    value="{{ old('email', $operator->user->email ?? '') }}"
                                    class="form-control @error('email') is-invalid @enderror" required>
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
                                            {{ (string) old('fakultas_id', $operator->fakultas_id) === (string) $f->id ? 'selected' : '' }}>
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
                                <label class="form-label">Password (opsional)</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Kosongkan jika tidak mengubah password">
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.operator.index') }}" class="btn btn-light">Kembali</a>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS load prodi --}}
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
                    const url = `/admin/prodi/by-fakultas/${fakultasId}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const ct = res.headers.get('content-type') || '';
                    if (!res.ok || !ct.includes('application/json')) {
                        const text = await res.text();
                        console.error('Bukan JSON:', text.slice(0, 200));
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

            fakultasSelect.addEventListener('change', (e) => loadProdi(e.target.value));

            // auto load saat pertama kali buka halaman edit
            loadProdi(
                "{{ old('fakultas_id', $operator->fakultas_id) }}",
                "{{ old('prodi_id', $operator->prodi_id) }}"
            );
        });
    </script>
@endsection
