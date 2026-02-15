<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\mahasiswa;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users::index');
    }

    public function operator()
    {
         $operators = User::where('role', 'operator')->orderBy('id','desc')->get();
         return view('users::operator', compact('operators'));
    }

    public function mahasiswa()
    {
         $mahasiswas = User::where('role', 'mahasiswa')->orderBy('id','desc')->get();
         return view('users::mahasiswa', compact('mahasiswas'));
    }
    public function wadek()
    {
         $wadeks = User::where('role', 'wadek')->orderBy('id','desc')->get();
         return view('users::wadek', compact('wadeks'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users::kontrol_mahasiswa.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $valdiasi =$request->validate([
            'name' => 'required|string|max:255',
            'nim' => ['required','string','max:50','unique:mahasiswa,nim'],
            'prodi' => 'required|string|max:255',
            'angkatan' => ['required','integer'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($valdiasi) {
            $user = User::create([
                'name' => $valdiasi['name'],
                'email' => $valdiasi['email'],
                'password' => bcrypt($valdiasi['password']),
                'role' => 'mahasiswa',
            ]);

            mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $valdiasi['nim'],
                'prodi' => $valdiasi['prodi'],
                'angkatan' => $valdiasi['angkatan'],
            ]);
        });

        return redirect()->route('admin.mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan.');

        User::create([
            'name' => $valdiasi['name'],
            'email' => $valdiasi['email'],
            'password' => bcrypt($valdiasi['password']),
            'role' => 'mahasiswa',
        ]);

        return redirect()->route('admin.mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
