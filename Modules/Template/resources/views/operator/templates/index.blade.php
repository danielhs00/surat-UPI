@extends('layouts.mantis')

@section('title', 'Hasil Pengajuan dari Wadek')
@include('components.mantis.header', ['role' => 'operator'])
@section('content')
<!-- [ Main Content ] start -->
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Template Surat</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <div class="row">

        {{-- table operator --}}
        <div class="col-md-12 col-2xl-8">
          <h5 class="mb-3 fs-3">Daftar Template Surat</h5>
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
                        <a href="{{ route('operator.template.create') }}" class="btn btn-primary">Tambah</a>
                    </div>
                </div>
            </li>


            
              <div class="table-responsive">
                <table id="pengajuanTable" class="table table-hover table-borderless mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Jenis</th>
                      <th>Status</th>
                      <th>File</th>
                      <th class="text-end">Diupload</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($templates as $t)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $t->nama_template }}</td>
                      <td>{{ $t->jenis_surat ?? '-' }}</td>
                      <td>{{ $t->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                      <td>
                        @if(!empty($t->google_docs_url))
                        <a href="{{ $t->google_docs_url }}" target="_blank">
                          Buka Link Template Surat
                        </a>
                        @else
                        <span class="text-muted">Link belum ada</span>
                        @endif
                      </td>
                      <td class="text-end">{{ $t->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
            {{-- end table operator --}}
        </div>
        </div>
    <!-- [ Main Content ] end -->
@endsection