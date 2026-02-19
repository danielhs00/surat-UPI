<?php

namespace Modules\Template\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Template\Models\Template;


class TemplateController extends Controller
{
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
        'nama_template' => 'required',
        'jenis_surat' => 'required',
        'google_docs_url' => 'required|url'
    ]);

    Template::create([
        'fakultas_id' => auth()->user()->fakultas_id,
        'uploaded_by' => auth()->id(),
        'nama_template' => $request->nama_template,
        'jenis_surat' => $request->jenis_surat,
        'deskripsi' => $request->deskripsi,
        'google_docs_url' => $request->google_docs_url,
        'is_active' => true,
    ]);

    return redirect()->route('operator.template.index')
        ->with('success', 'Template berhasil ditambahkan');
    }
}