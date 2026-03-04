<?php

namespace Modules\Template\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Template\Models\Template;

class TemplateController extends Controller
{

    // public function store(Request $request)
    // {
    //     // DEBUG: lihat semua data yang diterima
    //     dd([
    //         'all_data' => $request->all(),
    //         'has_file' => $request->hasFile('file_docx'),
    //         'file_info' => $request->hasFile('file_docx') ? [
    //             'original_name' => $request->file('file_docx')->getClientOriginalName(),
    //             'size' => $request->file('file_docx')->getSize(),
    //             'mime' => $request->file('file_docx')->getMimeType(),
    //             'error' => $request->file('file_docx')->getError(),
    //         ] : 'No file',
    //         'files' => $_FILES,
    //     ]);

    //     // ... rest of code
    // }
    public function index()
    {
        $fakultasId = auth()->user()->fakultas_id;

        $templates = Template::where('fakultas_id', $fakultasId)
            ->orderByDesc('id')
            ->get();

        return view('template::operator.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('template::operator.templates.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'jenis_surat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_docx' => 'required|file|mimes:doc,docx|max:5120', // Max 5MB
        ]);

        // Upload file
        $path = $request->file('file_docx')->store('templates', 'public');

        Template::create([
            'fakultas_id' => auth()->user()->fakultas_id,
            'uploaded_by' => auth()->id(),
            'nama_template' => $request->nama_template,
            'jenis_surat' => $request->jenis_surat,
            'deskripsi' => $request->deskripsi,
            'file_docx_path' => $path,
            'is_active' => true,
        ]);

        return redirect()->route('operator.template.index')
            ->with('success', 'Template berhasil ditambahkan');
    }

    // Modules/Template/Http/Controllers/TemplateController.php

    public function edit($id)
    {
        try {
            $template = Template::findOrFail($id);

            // Cek otorisasi
            if ($template->fakultas_id != auth()->user()->fakultas_id) {
                abort(403, 'Anda tidak memiliki akses ke template ini');
            }

            return view('template::operator.templates.edit', compact('template'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Template tidak ditemukan');
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error edit template: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Modules/Template/Http/Controllers/TemplateController.php

    public function update(Request $request, $id)
    {
        try {
            $template = Template::findOrFail($id);

            // Cek otorisasi
            if ($template->fakultas_id != auth()->user()->fakultas_id) {
                abort(403, 'Anda tidak memiliki akses ke template ini');
            }

            $request->validate([
                'nama_template' => 'required|string|max:255',
                'jenis_surat' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'file_docx' => 'nullable|file|mimes:doc,docx|max:5120',
                'is_active' => 'nullable|boolean'
            ]);

            $data = [
                'nama_template' => $request->nama_template,
                'jenis_surat' => $request->jenis_surat,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active') ? true : false,
            ];

            // Upload file baru jika ada
            if ($request->hasFile('file_docx')) {
                // Hapus file lama
                if ($template->file_docx_path && Storage::disk('public')->exists($template->file_docx_path)) {
                    Storage::disk('public')->delete($template->file_docx_path);
                }

                $path = $request->file('file_docx')->store('templates', 'public');
                $data['file_docx_path'] = $path;
            }

            $template->update($data);

            return redirect()
                ->route('operator.template.index')
                ->with('success', 'Template "' . $template->nama_template . '" berhasil diperbarui');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('operator.template.index')
                ->with('error', 'Template tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Error update template: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        // Cek otorisasi
        if ($template->fakultas_id != auth()->user()->fakultas_id) {
            abort(403);
        }

        // Hapus file
        if ($template->file_docx_path && Storage::disk('public')->exists($template->file_docx_path)) {
            Storage::disk('public')->delete($template->file_docx_path);
        }

        $template->delete();

        return redirect()->route('operator.template.index')
            ->with('success', 'Template berhasil dihapus');
    }

    // Method untuk download template
    public function download($id)
    {
        $template = Template::findOrFail($id);

        // Cek otorisasi (untuk mahasiswa)
        if (auth()->user()->role === 'mahasiswa') {
            $mahasiswa = \App\Models\Mahasiswa::where('user_id', auth()->id())->first();
            if ($mahasiswa->fakultas_id != $template->fakultas_id) {
                abort(403);
            }
        }

        if (!$template->file_docx_path || !Storage::disk('public')->exists($template->file_docx_path)) {
            abort(404, 'File template tidak ditemukan');
        }

        $filename = str_replace(' ', '_', $template->nama_template) . '.docx';
        return Storage::disk('public')->download($template->file_docx_path, $filename);
    }
}
