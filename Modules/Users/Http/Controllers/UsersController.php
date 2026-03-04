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

    public function editOperator(Operator $operator)
    {
        $operator->load(['user', 'fakultas', 'prodi']);
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
}
