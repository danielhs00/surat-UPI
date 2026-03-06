@extends('layouts.mantis')

@section('title', 'Admin | Operator')
@include('components.mantis.header', ['role' => 'admin'])

@section('content')
    <div class="pc-content">
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

        {{-- alert --}}
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

            <div class="col-12">
                <div class="card tbl-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Operator</h5>
                        <a href="{{ route('admin.operator.create') }}" class="btn btn-primary">Tambah Operator</a>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <input type="search" id="searchInput" class="form-control w-25" placeholder="Cari disini...">
                        </div>

                        <div class="table-responsive">
                            <table id="operatorTable" class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:60px">No</th>
                                        <th style="width:120px">ID</th>
                                        <th>Nama Operator</th>
                                        <th>Email</th>
                                        <th>Fakultas</th>
                                        <th>Prodi</th>
                                        <th>Status</th>
                                        <th class="text-end">Dibuat</th>
                                        <th class="text-end" style="width:180px">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($operators as $i => $op)
                                        @php $aktif = (bool) ($op->is_active ?? true); @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $op->id }}</td>
                                            <td>{{ $op->user->name ?? '-' }}</td>
                                            <td>{{ $op->user->email ?? '-' }}</td>
                                            <td>{{ $op->fakultas->nama_fakultas ?? '-' }}</td>
                                            <td>{{ $op->prodi->nama_prodi ?? '-' }}</td>
                                            <td>
                                                @if ($aktif)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ optional($op->created_at)->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.operator.edit', $op->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>

                                                <form action="{{ route('admin.operator.destroy', $op->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin hapus operator ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Belum ada data operator.</td>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('searchInput');
            const table = document.getElementById('operatorTable');
            if (!input || !table) return;

            input.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
