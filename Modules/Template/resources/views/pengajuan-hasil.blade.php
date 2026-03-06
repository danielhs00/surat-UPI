@extends('layouts.mantis')

@section('title', 'Hasil Pengajuan dari Wadek')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Hasil Pengajuan (Wadek)</h4>
                    <a href="{{ route('operator.pengajuan') }}" class="btn btn-light btn-sm">Kembali</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Template</th>
                                    <th>Status</th>
                                    <th>Nomor Surat</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Catatan Wadek</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pengajuans as $i => $p)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $p->user->mahasiswa->nim ?? 'N/A' }}</td>
                                        <td>{{ $p->user->name ?? 'N/A' }}</td>
                                        <td>{{ $p->template->nama_template ?? 'N/A' }}</td>

                                        <td>
                                            @if ($p->status === 'signed')
                                                <span class="badge bg-success">SIGNED</span>
                                            @elseif ($p->status === 'rejected')
                                                <span class="badge bg-danger">REJECTED</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $p->status }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $p->nomor_surat ?? '-' }}</td>
                                         <td>
                                            {{ optional($p->approved_at ?: $p->updated_at)->format('d M Y H:i') }}
                                        </td>
                                        <td>{{ $p->catatan_wadek ?? '-' }}</td>

                                        <td class="text-end">
                                            @php
                                                $hasPdf = !empty($p->signed_pdf_path) || !empty($p->pdf_path);
                                            @endphp

                                            @if ($hasPdf)
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="{{ route('operator.pengajuan.pdf', $p->id) }}" target="_blank"
                                                    rel="noopener">
                                                    {{ !empty($p->signed_pdf_path) ? 'Lihat PDF TTD' : 'Lihat PDF' }}
                                                </a>
                                            @else
                                                <span class="text-muted">PDF belum ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Belum ada hasil dari Wadek.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
