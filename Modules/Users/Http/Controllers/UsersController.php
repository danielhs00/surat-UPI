<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\Operator;
use Modules\Master\Models\Fakultas; // ✅ PENTING: yang benar
use App\Models\Prodi;               // ✅ untuk dropdown prodi by fakultas

class UsersController extends Controller
{
    public function dashboard()
    {
        $jumlah_mahasiswa = \App\Models\Mahasiswa::count();
        $jumlah_operator  = Operator::count();

        $operators = Operator::with(['user', 'fakultas', 'prodi'])
            ->orderByDesc('id')
            ->get();

        $fakultas = Fakultas::orderBy('nama_fakultas')->get();

        return view('users::index', compact(
            'jumlah_mahasiswa',
            'jumlah_operator',
            'operators',
            'fakultas'
        ));
    }

    public function index()
    {
        return view('users::index');
    }

    public function operator()
    {
        $operators = Operator::with(['user', 'fakultas', 'prodi'])
            ->orderByDesc('id')
            ->get();

        return view('users::operator.index', compact('operators'));
    }

    public function tambah_operator()
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        return view('users::operator.tambah', compact('fakultas'));
    }

    public function storeOperator(Request $request)
    {
        $validasi = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:8|confirmed',
            'fakultas_id' => 'required|exists:fakultas,id',
            'prodi_id'    => 'required|exists:prodi,id',
        ]);

        if (Operator::where('fakultas_id', $validasi['fakultas_id'])->exists()) {
            return back()->withInput()->withErrors([
                'fakultas_id' => 'Operator untuk fakultas ini sudah ada.'
            ]);
        }

        DB::transaction(function () use ($validasi) {
            $user = User::create([
                'name'       => $validasi['name'],
                'email'      => $validasi['email'],
                'password'   => Hash::make($validasi['password']),
                'role'       => 'operator',
                'fakultas_id' => $validasi['fakultas_id'], // kalau kolom ini ada di users
            ]);

            Operator::create([
                'user_id'     => $user->id,
                'fakultas_id' => $validasi['fakultas_id'],
                'prodi_id'    => $validasi['prodi_id'],
                'is_active'   => 1,
            ]);
        });

        return redirect()->route('admin.dashboard')->with('success', 'Operator berhasil ditambahkan.');
    }

    public function editOperator($id)
    {
        $operator = Operator::with(['user', 'fakultas', 'prodi'])->findOrFail($id);
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();

        return view('users::operator.edit', compact('operator', 'fakultas'));
    }

    public function toggleOperator(Operator $operator)
    {
        if (!Schema::hasColumn('operator', 'is_active')) {
            return back()->withErrors(['status' => 'Kolom is_active belum ada di tabel operator.']);
        }

        $operator->is_active = !(bool) $operator->is_active;
        $operator->save();

        return back()->with('success', 'Status operator berhasil diubah.');
    }

    // Tambahkan method ini di UsersController.php
    public function getProdiByFakultas($fakultasId)
    {
        try {
            $prodi = DB::table('prodi')
                ->where('fakultas_id', $fakultasId)
                ->orderBy('nama_prodi')
                ->get(['id', 'nama_prodi']);

            return response()->json($prodi);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateOperator(\Illuminate\Http\Request $request, $id)
    {
        $operator = \App\Models\Operator::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'fakultas_id' => 'required|integer',
            'prodi_id' => 'required|integer',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $operator->user->name  = $request->name;
        $operator->user->email = $request->email;

        if ($request->filled('password')) {
            $operator->user->password = bcrypt($request->password);
        }
        $operator->user->save();

        $operator->fakultas_id = $request->fakultas_id;
        $operator->prodi_id    = $request->prodi_id;
        $operator->save();

        return redirect()->route('admin.dashboard')->with('success', 'Operator berhasil diupdate.');
    }

    public function deleteOperator($id)
    {
        $operator = \App\Models\Operator::findOrFail($id);

        $operator->delete();

        return redirect()->back()->with('success', 'Operator berhasil dihapus.');
    }
}
