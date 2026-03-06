@extends('layouts.mantis')

@section('title', 'Surat Selesai')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Riwayat Surat (Selesai & Ditolak)</h4>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Template</th>
                            <th>Nomor Surat</th>
                            <th>Status</th>
                            <th>Tanggal Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $index => $doc)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $doc->template->nama_template ?? '-' }}</td>
                                <td>{{ $doc->nomor_surat ?? '-' }}</td>

                                <td>
                                    @if ($doc->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($doc->status === 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $doc->status }}</span>
                                    @endif
                                </td>

                                <td>{{ optional($doc->updated_at)->format('d M Y H:i') }}</td>

                                <td>
                                    @if ($doc->status === 'completed')
                                        <a href="{{ route('mahasiswa.documents.downloadPdf', $doc->id) }}"
                                            class="btn btn-success btn-sm">
                                            Download
                                        </a>
                                    @elseif ($doc->status === 'rejected')
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#catatanModal{{ $doc->id }}">
                                                Lihat Alasan
                                            </button>

                                            <form action="{{ route('mahasiswa.documents.resubmit', $doc->id) }}"
                                                method="POST" onsubmit="return confirm('Ajukan ulang dokumen ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Ajukan Ulang
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada surat selesai atau ditolak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @foreach ($documents as $doc)
                    @if ($doc->status === 'rejected')
                        <div class="modal fade" id="catatanModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Alasan Penolakan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger mb-0">
                                            {{ $doc->catatan_operator ?: 'Tidak ada catatan.' }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                
            </div>
        </div>
    </div>
@endsection
