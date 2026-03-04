@extends('layouts.mantis')

@section('title', 'Surat UPI | Wadek')
@include('components.mantis.header', ['role' => 'admin'])



@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Wakil Dekan</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <div class="row">

        {{-- table wadek --}}
        <div class="col-md-12 col-2xl-8">
          <h5 class="mb-3 fs-3">Daftar Wakil Dekan</h5>
          <div class="card tbl-card">
            <div class="card-body">
              <li class="pc-h-item d-none d-md-block">
                <div class="row align-items-center">
                    <div class="col">
                        <form class="header-search me-2">
                            <input type="search" id="searchInput" class="form-control w-25" placeholder="Cari disini...">
                        </form>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('tambah.wadek') }}" class="btn btn-primary">Tambah Wakil Dekan</a>
                    </div>
                </div>
            </li>


            
              <div class="table-responsive">
                <table id="pengajuanTable" class="table table-hover table-borderless mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>ID Wakil Dekan</th>
                      <th>Nama Wakil Dekan</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th class="text-end">Tanggal Dibuat</th>
                    </tr>
                  </thead>
                  <tbody>
                     @foreach ($wadeks as $index => $wadek)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $wadek->id }}</td>
                      <td>{{ $wadek->name }}</td>
                      <td>{{ $wadek->email }}</td>
                      <td>{{ $wadek->role }}</td>
                      <td class="text-end">{{ $wadek->created_at->format('d M Y') }}</td>
                      <td class="text-end">
                        <a href="{{ route('edit.wadek', $wadek->id) }}" class="btn btn-warning btn-sm"> Edit </a>
                        <form action="{{ route('hapus.wadek', $wadek->id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus wadek?')">
                            Hapus
                          </button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
            {{-- end table wadek --}}
        </div>
        </div>
@endsection