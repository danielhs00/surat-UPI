{{-- Modules/Template/resources/views/operator/templates/edit.blade.php --}}

@extends('layouts.mantis')

@section('title', 'Edit Template Surat')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Edit Template Surat</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('operator.template.index') }}">Template Surat</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Template</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Form Edit Template</h5>
                    </div>
                    <div class="card-body">

                        {{-- Tampilkan error validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="ti ti-alert-triangle"></i> Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form Edit --}}
                        <form action="{{ route('operator.template.update', $template->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Nama Template --}}
                            <div class="mb-3">
                                <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" name="nama_template"
                                    class="form-control @error('nama_template') is-invalid @enderror"
                                    value="{{ old('nama_template', $template->nama_template) }}" required>
                                @error('nama_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jenis Surat --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                                <input type="text" name="jenis_surat"
                                    class="form-control @error('jenis_surat') is-invalid @enderror"
                                    value="{{ old('jenis_surat', $template->jenis_surat) }}" required>
                                @error('jenis_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- File Saat Ini --}}
                            @if ($template->file_docx_path)
                                <div class="mb-3">
                                    <label class="form-label">File Saat Ini</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-file-text text-primary me-2" style="font-size: 1.5rem;"></i>
                                            <div class="flex-grow-1">
                                                <a href="{{ Storage::url($template->file_docx_path) }}" target="_blank"
                                                    class="text-primary fw-bold">
                                                    {{ basename($template->file_docx_path) }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    @php
                                                        $filePath = storage_path(
                                                            'app/public/' . $template->file_docx_path,
                                                        );
                                                        if (file_exists($filePath)) {
                                                            $size = filesize($filePath);
                                                            echo 'Ukuran: ' . number_format($size / 1024, 2) . ' KB';
                                                        }
                                                    @endphp
                                                </small>
                                            </div>
                                            <a href="{{ Storage::url($template->file_docx_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <i class="ti ti-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Upload File Baru --}}
                            <div class="mb-3">
                                <label class="form-label">Ganti File Template (Word)</label>
                                <input type="file" name="file_docx"
                                    class="form-control @error('file_docx') is-invalid @enderror"
                                    accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                @error('file_docx')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Kosongkan jika tidak ingin mengganti file. Format .doc/.docx, maks 5MB.
                                </small>
                            </div>

                            {{-- Status Aktif --}}
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Template Aktif
                                    </label>
                                    <small class="text-muted d-block">
                                        Jika tidak aktif, template tidak akan terlihat oleh mahasiswa
                                    </small>
                                </div>
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('operator.template.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-light me-2">
                                        <i class="ti ti-refresh"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-save"></i> Update Template
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
