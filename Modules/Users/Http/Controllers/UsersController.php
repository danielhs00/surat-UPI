<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Fakultas;
use Illuminate\Support\Facades\DB;
use Modules\Users\Entities\Wadek;
use App\Models\Operator;
use Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users::create');
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
        return view('users::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('users::edit');
    }

    public function operator()
    {
        $operators = User::select('users.*', 'fakultas.nama_fakultas')
            ->leftJoin('fakultas', 'fakultas.id', '=', 'users.fakultas_id')
            ->where('users.role', 'operator')
            ->orderByDesc('users.id')
            ->get();

        return view('users::operator.index', compact('operators'));
    }

    public function tambah_operator()
    {
        $fakultas = DB::table('fakultas')->orderBy('nama_fakultas')->get();
        return view('users::operator.tambah', compact('fakultas'));
    }

public function storeOperator(Request $request)
{
    $validasi = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'fakultas_id' => 'required|exists:fakultas,id',
    ]);

    // ✅ cek yang benar: berdasarkan tabel operator (karena ada UNIQUE di sana)
    if (Operator::where('fakultas_id', $validasi['fakultas_id'])->exists()) {
        return back()
            ->withInput()
            ->withErrors(['fakultas_id' => 'Operator untuk fakultas ini sudah ada.']);
    }

    DB::transaction(function () use ($validasi) {

        $user = User::create([
            'name' => $validasi['name'],
            'email' => $validasi['email'],
            'password' => Hash::make($validasi['password']),
            'role' => 'operator',
            'fakultas_id' => $validasi['fakultas_id'],
        ]);

        Operator::create([
            'user_id' => $user->id,
            'fakultas_id' => $validasi['fakultas_id'],
        ]);
    });

    return redirect()->route('admin.operator')->with('success', 'Operator berhasil ditambahkan.');
}

    public function wadek()
    {
        $wadeks = User::select('users.*', 'fakultas.nama_fakultas')
            ->leftJoin('fakultas', 'fakultas.id', '=', 'users.fakultas_id')
            ->where('users.role', 'wadek')
            ->orderByDesc('users.id')
            ->get();

        return view('users::wadek.index', compact('wadeks'));
    }

    public function tambah_wadek()
    {
         $fakultas = DB::table('fakultas')->orderBy('nama_fakultas')->get();
         return view('users::wadek.tambah', compact('fakultas'));
    }

    public function storeWadek(Request $request)
    {
        $validasi = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'fakultas_id' => 'required|exists:fakultas,id',
    ]);

    $sudahAda = User::where('role', 'wadek')
        ->where('fakultas_id', $validasi['fakultas_id'])
        ->exists();

    if ($sudahAda) {
        return back()
            ->withInput()
            ->withErrors(['fakultas_id' => 'Wadek untuk fakultas ini sudah ada.']);
    }

    DB::transaction(function () use ($validasi) {
        User::create([
            'name' => $validasi['name'],
            'email' => $validasi['email'],
            'password' => bcrypt($validasi['password']),
            'role' => 'wadek',
            'fakultas_id' => $validasi['fakultas_id'],
        ]);
    });

    return redirect()->route('admin.wadek')->with('success', 'Wadek berhasil ditambahkan.');
    
    }

    public function updateOperator(Request $request, $id)
{
    $validasi = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        'fakultas_id' => 'required|exists:fakultas,id',
    ]);

    // Cek unik operator per fakultas (kecuali dirinya sendiri)
    $sudahAda = User::where('role', 'operator')
        ->where('fakultas_id', $validasi['fakultas_id'])
        ->where('id', '!=', $id)
        ->exists();

    if ($sudahAda) {
        return back()->withInput()->withErrors(['fakultas_id' => 'Fakultas ini sudah punya operator.']);
    }

    $user = User::findOrFail($id);
    $user->update([
        'name' => $validasi['name'],
        'email' => $validasi['email'],
        'fakultas_id' => $validasi['fakultas_id'],
    ]);

    return redirect()->route('admin.operator')->with('success', 'Operator berhasil diperbarui.');
    }

    public function editOperator($id)
    {
        $user = User::findOrFail($id);
        $fakultas = DB::table('fakultas')->orderBy('nama_fakultas')->get();

        return view('users::operator.edit', compact('user', 'fakultas'));
    }

    public function destroyOperator($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.operator')->with('success', 'Operator berhasil dihapus.');
    }

    public function editWadek($id)
    {
        $user = User::findOrFail($id);
        $fakultas = DB::table('fakultas')->orderBy('nama_fakultas')->get();
        
        return view('users::wadek.edit', compact('user','fakultas'));
    }

    public function updateWadek(Request $request, $id)
    {
        $validasi = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        'fakultas_id' => 'required|exists:fakultas,id',
    ]);

    // unik 1 wadek per fakultas kecuali dirinya
    $sudahAda = User::where('role', 'wadek')
        ->where('fakultas_id', $validasi['fakultas_id'])
        ->where('id', '!=', $id)
        ->exists();

    if ($sudahAda) {
        return back()->withInput()->withErrors(['fakultas_id' => 'Fakultas ini sudah punya wadek.']);
    }

    $user = User::findOrFail($id);
    $user->update([
        'name' => $validasi['name'],
        'email' => $validasi['email'],
        'fakultas_id' => $validasi['fakultas_id'],
    ]);

    return redirect()->route('admin.wadek')->with('success', 'Wadek berhasil diperbarui.');
    
    }

    public function destroyWadek($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.wadek')->with('success', 'Wadek berhasil dihapus.');
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
