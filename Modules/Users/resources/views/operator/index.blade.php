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
                <h5 class="m-b-10">Operator</h5>
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
          <h5 class="mb-3 fs-3">Daftar Operator</h5>
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
                        <a href="{{ route('tambah.operator') }}" class="btn btn-primary">Tambah Operator</a>
                    </div>
                </div>
            </li>


            
              <div class="table-responsive">
                <table id="pengajuanTable" class="table table-hover table-borderless mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No Operator</th>
                      <th>Nama Operator</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th class="text-end">Tanggal Dibuat</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($operators as $index => $operator)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $operator->id }}</td>
                      <td>{{ $operator->name }}</td>
                      <td>{{ $operator->email }}</td>
                      <td>{{ $operator->role }}</td>
                      <td class="text-end">{{ $operator->created_at->format('d M Y') }}</td>
                      <td class="text-end">
                        <a href="{{ route('edit.operator', $operator->id) }}" class="btn btn-warning btn-sm">
                          Edit
                        </a>
                        <form action="{{ route('hapus.operator', $operator->id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE') 
                          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus operator ini?')"> Hapus </button>
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
            {{-- end table operator --}}
        </div>
        </div>
@endsection