@extends('layouts.mantis')

@section('title', 'Edit Template Surat')

@section('content')
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Edit Template Surat</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 p-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Form Edit Template</h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('operator.template.update', $template->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" name="nama_template"
                                    class="form-control @error('nama_template') is-invalid @enderror"
                                    value="{{ old('nama_template', $template->nama_template) }}" required>
                                @error('nama_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                                <input type="text" name="jenis_surat"
                                    class="form-control @error('jenis_surat') is-invalid @enderror"
                                    value="{{ old('jenis_surat', $template->jenis_surat) }}" required>
                                @error('jenis_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">File Template (Word)</label>
                                <input type="file" name="file_docx"
                                    class="form-control @error('file_docx') is-invalid @enderror"
                                    accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                @error('file_docx')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengganti file
                                    template.</small>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_active"
                                    name="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Template aktif
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('operator.template.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
