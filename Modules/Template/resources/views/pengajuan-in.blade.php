@extends('layouts.mantis')

@section('title', 'Daftar Pengajuan Surat Mahasiswa')
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
        <div class="col-md-12 col-2xl-8">
            <h5 class="mb-3 fs-3">Daftar Pengajuan Mahasiswa</h5>

            <div class="card tbl-card">
                <div class="card-body">
                    <div class="d-none d-md-block mb-3 w-25">
                        <div class="row align-items-center">
                            <div class="col">
                                <form class="header-search me-2" onsubmit="return false;">
                                    <input type="text" id="myInput" class="form-control"
                                        placeholder="Cari pengajuan mahasiswa...">
                                </form>
                            </div>
                            <div class="col-auto">
                                {{-- <a href="{{ route('tambah.mahasiswa') }}" class="btn btn-primary">Tambah Mahasiswa</a> --}}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover table-borderless mb-0">
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
                                @forelse ($pengajuans as $index => $pengajuan)
                                    <tr class="template-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $pengajuan->user->mahasiswa->nim ?? 'N/A' }}</td>
                                        <td>{{ $pengajuan->user->name ?? 'N/A' }}</td>
                                        <td>{{ $pengajuan->template->nama_template ?? 'N/A' }}</td>
                                        <td>{{ $pengajuan->status ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            {{ optional($pengajuan->created_at)->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('operator.pengajuan.edit', $pengajuan->id) }}"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <form action="{{ route('operator.pengajuan.destroy', $pengajuan->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin hapus pengajuan ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Belum ada data pengajuan mahasiswa
                                        </td>
                                    </tr>
                                @endforelse

                                <tr id="noDataRow" style="display: none;">
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('myInput');
        const rows = document.querySelectorAll('#myTable tbody tr.template-row');
        const noDataRow = document.getElementById('noDataRow');
        const emptyRow = document.getElementById('emptyRow');

        if (input) {
            input.addEventListener('keyup', function () {
                const filter = this.value.toUpperCase();
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const rowText = row.textContent || row.innerText;

                    if (rowText.toUpperCase().indexOf(filter) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyRow) {
                    emptyRow.style.display = filter === '' ? '' : 'none';
                }

                if (noDataRow && rows.length > 0) {
                    noDataRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            });
        }
    });
</script>
@endpush