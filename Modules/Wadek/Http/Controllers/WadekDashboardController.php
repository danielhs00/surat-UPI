<?php

namespace Modules\Wadek\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mahasiswa\Models\StudentDocument;
use App\Models\wadek As Wadek;

class WadekDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wdk = wadek::where('user_id', auth()->id())->first();

        $documents = StudentDocument::with(['user.mahasiswa', 'template'])
            ->where('status', 'sent_to_wadek')
            ->when($wdk, function ($q) use ($wdk) {
                $q->whereHas('template', function ($t) use ($wdk) {
                    $t->where('fakultas_id', $wdk->fakultas_id);
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('wadek::dashboard', compact('documents', 'wdk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wadek::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('wadek::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('wadek::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
