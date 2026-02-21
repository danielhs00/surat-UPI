@extends('mahasiswa::components.layouts.mantis')

@section('content')
    <div class="container">

        <h4>Daftar Surat Menunggu Persetujuan</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>s
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $doc)
                    <tr>
                        <td>{{ $doc->user->name ?? '-' }}</td>
                        <td>{{ $doc->title }}</td>
                        <td>{{ $doc->status }}</td>
                        <td>
                            <a href="{{ route('wadek.documents.show', $doc->id) }}" class="btn btn-sm btn-primary">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
