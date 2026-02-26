@extends('layouts.mantis')

@section('title', 'Dashboard Admin')
@include('components.mantis.header', ['role' => 'admin'])

@section('content')
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Dashboard</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Mahasiswa</h6>
              <h4 class="mb-3">{{ $jumlah_mahasiswa ?? 0 }}<span class="badge bg-light-primary border border-primary"></span></h4>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Operator</h6>
              <h4 class="mb-3">{{ $jumlah_operator ?? 0 }}<span class="badge bg-light-success border border-success"></span></h4>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Surat</h6>
              <h4 class="mb-3">18,800 <span class="badge bg-light-warning border border-warning"></span></h4>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Total Sales</h6>
              <h4 class="mb-3">$35,078 <span class="badge bg-light-danger border border-danger"></span></h4>
            </div>
          </div>
        </div>

        {{-- table pengajuan --}}
        <div class="col-md-12 col-2xl-8">
          <h5 class="mb-3">Daftar Pengajuan</h5>
          <div class="card tbl-card">
            <div class="card-body">
              <li class="pc-h-item d-none d-md-inline-flex">
                <form class="header-search">
                  <input type="search" id="searchInput" class="form-control" placeholder="Cari disini...">
                </form>
              </li>
              <div class="table-responsive">
                <table id="pengajuanTable" class="table table-hover table-borderless mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No Pengajuan</th>
                      <th>Nama Mahasiswa</th>
                      <th>Jenis Surat</th>
                      <th>Status</th>
                      <th class="text-end">Tanggal Pengajuan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($pengajuans as $index => $pengajuan)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $pengajuan->user->mahasiswa->nim ?? 'N/A' }}</td>
                                                <td>{{ $pengajuan->user->name ?? 'N/A' }}</td>
                                                <td>{{ $pengajuan->template->nama_template ?? 'N/A' }}</td>
                                                <td>{{ $pengajuan->status }}</td>
                                                <td class="text-end">{{ $pengajuan->created_at->format('d M Y') }}
                                                </td>

                                                <td class="text-end">
                                                    <a href="{{ route('operator.pengajuan.edit', $pengajuan->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>

                                                    <form
                                                        action="{{ route('operator.pengajuan.destroy', $pengajuan->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Yakin hapus mahasiswa ini?')">
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
        {{-- end table pengajuan --}}
        
      </div>
    </div>

@endsection