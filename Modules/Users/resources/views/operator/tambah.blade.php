@extends('layouts.mantis')

@section('title', 'Admin | Operator')
@include('components.mantis.header', ['role' => 'admin'])


<!-- [ Main Content ] start -->
@section('content')
<div class="pc-content">

  <!-- Header -->
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h5 class="m-b-10">Tambah Operator</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Section -->
  <div class="row">
    <div class="col-lg-12 p-4">
      <div class="card">
        <div class="card-body">

          <form action="{{ route('admin.operator.store') }}" method="POST">
            @csrf

            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label>Fakultas</label>
              <select name="fakultas_id" id="fakultas_id"
                class="form-control @error('fakultas_id') is-invalid @enderror" required>
                <option value="">-- Pilih Fakultas --</option>
                @foreach($fakultas as $f)
                <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                @endforeach
              </select>
              @error('fakultas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label>Prodi</label>
              <select name="prodi_id" id="prodi_id" class="form-control @error('prodi_id') is-invalid @enderror"
                required>
                <option value="">-- Pilih Prodi --</option>
              </select>
              @error('prodi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
              <label>Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.operator.index') }}" class="btn btn-secondary">Kembali</a>
              <button class="btn btn-primary">Simpan</button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fakultasSelect = document.getElementById('fakultas_id');
    const prodiSelect = document.getElementById('prodi_id');

    fakultasSelect.addEventListener('change', function () {
        const fakultasId = this.value;

        prodiSelect.innerHTML = '<option value="">-- Pilih Prodi --</option>';

        if (!fakultasId) return;

        fetch(`/admin/prodi/by-fakultas/${fakultasId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data prodi');
                }
                return response.json();
            })
            .then(data => {
                data.forEach(prodi => {
                    const option = document.createElement('option');
                    option.value = prodi.id;
                    option.textContent = prodi.nama_prodi;
                    prodiSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
});
</script>
@endsection
<!-- [ Main Content ] end -->