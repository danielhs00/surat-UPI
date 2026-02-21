<?php

namespace Modules\Mahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Mahasiswa\Models\Template;
use Modules\Mahasiswa\Models\StudentDocument;

class MahasiswaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $templates = Template::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $status = $request->query('status', 'all');

        $recentDocsQuery = StudentDocument::where('user_id', auth()->id());

        if ($status !== 'all') {
            $recentDocsQuery->where('status', $status);
        }

        $recentDocs = $recentDocsQuery
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get();

        return view(
            'mahasiswa::components.mahasiswa.dashboard',
            compact('recentDocs', 'status')
        );
    }
}
