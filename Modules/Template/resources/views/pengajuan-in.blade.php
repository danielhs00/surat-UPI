@extends('layouts.mantis')

@section('title', 'Daftarnya Pengajuan Surat Mahasiswa')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Mahasiswa</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">

                {{-- table mahasiswa --}}
                <div class="col-md-12 col-2xl-8">
                    <h5 class="mb-3 fs-3">Daftar Pengajuan Mahasiswa</h5>
                    <div class="card tbl-card">
                        <div class="card-body">
                            <li class="pc-h-item d-none d-md-block">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <form class="header-search me-2">
                                            <input type="search" id="searchInput" class="form-control w-25"
                                                placeholder="Cari disini...">
                                        </form>
                                    </div>
                                    <div class="col-auto">
                                        {{-- <a href="{{ route('tambah.mahasiswa') }}" class="btn btn-primary">Tambah Mahasiswa</a> --}}
                                    </div>
                                </div>
                            </li>



                            <div class="table-responsive">
                                <table id="pengajuanTable" class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Jenis Surat</th>
                                            <th>Status</th>
                                            <th class="text-end">Tanggal Dibuat</th>
                                            <th class="text-end">Aksi</th>
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
                {{-- end table mahasiswa --}}
            </div>
        </div>

@endsection