@extends('layouts.mantis')

@section('title', 'Tambah Template Surat')

@section('content')
<div class="pc-content">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Tambah Template Surat</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 p-4">
            <div class="card">
                <div class="card-header">
                    <h5>Form Tambah Template</h5>
                </div>
                <div class="card-body">
                    
                    {{-- Tampilkan error jika ada --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM YANG BENAR --}}
                    <form action="{{ route('operator.template.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data">  {{-- WAJIB ADA --}}
                        @csrf

                        {{-- Nama Template --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama_template" 
                                   class="form-control @error('nama_template') is-invalid @enderror" 
                                   value="{{ old('nama_template') }}"
                                   required>
                            @error('nama_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Surat --}}
                        <div class="mb-3">
                            <label class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="jenis_surat" 
                                   class="form-control @error('jenis_surat') is-invalid @enderror" 
                                   value="{{ old('jenis_surat') }}"
                                   required>
                            @error('jenis_surat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" 
                                      class="form-control @error('deskripsi') is-invalid @enderror" 
                                      rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FILE UPLOAD --}}
                        <div class="mb-3">
                            <label class="form-label">File Template (Word) <span class="text-danger">*</span></label>
                            <input type="file" 
                                   name="file_docx"  {{-- NAMA FIELD INI PENTING --}}
                                   class="form-control @error('file_docx') is-invalid @enderror" 
                                   accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                   required>
                            @error('file_docx')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Upload file template dalam format Word (.doc atau .docx). Maksimal 5MB.
                            </small>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('operator.template.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-save"></i> Simpan Template
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection