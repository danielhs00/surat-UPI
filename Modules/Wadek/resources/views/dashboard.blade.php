@extends('layouts.mantis')
@section('title', 'Dashboard Wadek')
@include('components.mantis.header', ['role' => 'wadek'])

@section('content')
    <div class="container">

        <h4>Daftar Surat Menunggu Persetujuan</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
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
