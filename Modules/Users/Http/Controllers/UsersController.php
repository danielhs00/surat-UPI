<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\mahasiswa;
use App\Models\operator;
use App\Models\wadek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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


    /// mulai edit  dan hapus mahasiswa
    public function editMahasiswa($id)
    {
        $user = User::findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', $id)->first();
        return view('users::kontrol_mahasiswa.edit', compact('user','mahasiswa'));
    }

    public function updateMahasiswa(Request $request, $id)
    {
        $valdiasi =$request->validate([
            'name' => 'required|string|max:255',
            'nim' => ['required','string','max:50','unique:mahasiswa,nim,'.$id.',user_id'],
            'prodi' => 'required|string|max:255',
            'angkatan' => ['required','integer'],
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        DB::transaction(function () use ($valdiasi, $id) {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $valdiasi['name'],
                'email' => $valdiasi['email'],
            ]);

            $mahasiswa = Mahasiswa::where('user_id', $id)->first();
            $mahasiswa->update([
                'nim' => $valdiasi['nim'],
                'prodi' => $valdiasi['prodi'],
                'angkatan' => $valdiasi['angkatan'],
            ]);
        });

        return redirect()->route('admin.mahasiswa')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

     public function destroyMahasiswa($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            Mahasiswa::where('user_id', $id)->delete();
            $user->delete();
        });

        return redirect()->route('admin.mahasiswa')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    // SELESAI EDIT DAN HAPUS MAHASISWA

    // mulai edit dan hapus operator
    public function editOperator($id)
    {
        $user = User::findOrFail($id);
        $operator = operator::where('user_id', $id)->first();
        return view('users::kontrol_operator.edit', compact('user', 'operator'));
    }

    public function updateOperator(Request $request, $id)
    {
        $valdiasi =$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        DB::transaction(function () use ($valdiasi, $id) {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $valdiasi['name'],
                'email' => $valdiasi['email'],
            ]);

            $operator = operator::where('user_id', $id)->first();
            $operator->update([
            ]);
        });

        return redirect()->route('admin.operator')->with('success', 'Operator berhasil diperbarui.');
    }

     public function destroyOperator($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            operator::where('user_id', $id)->delete();
            $user->delete();
        });

        return redirect()->route('admin.operator')->with('success', 'Operator berhasil dihapus.');
    }
        // SELESAI EDIT DAN HAPUS OPERATOR




    /**
     * Show the form for creating a new resource.
     */
    // mulai tambah mahasiswa
    public function tambah_mahasiswa()
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
    // SELESAI TAMBAH MAHASISWA

//tambah operator
public function tambah_operator()
    {
        return view('users::kontrol_operator.tambah');
    }
public function storeOperator(Request $request)
{
    $validasi = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    DB::transaction(function () use ($validasi) {
    $user = User::create([
        'name' => $validasi['name'],
        'email' => $validasi['email'],
        'password' => bcrypt($validasi['password']),
        'role' => 'operator',
    ]);

    if ($user->role !== 'operator') {
        $user->role = 'operator';
        $user->save();
    }

    operator::create([
        'user_id' => $user->id,
        'name' => $validasi['name'],
        'email' => $validasi['email'],
        'password' => bcrypt($validasi['password']),
    ]);
});

    return redirect()->route('admin.operator')->with('success', 'Operator berhasil ditambahkan.');
}
    // SELESAI TAMBAH OPERATOR

    public function tambah_wadek()
    {
        return view('users::kontrol_wadek.tambah');
    }

    public function storeWadek(Request $request)
    {
        $validasi = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($validasi) {
            $user = User::create([
                'name' => $validasi['name'],
                'email' => $validasi['email'],
                'password' => bcrypt($validasi['password']),
                'role' => 'wadek',
            ]);

            if ($user->role !== 'wadek') {
                $user->role = 'wadek';
                $user->save();
            }

            wadek::create([
                'user_id' => $user->id,
                'name' => $validasi['name'],
                'email' => $validasi['email'],
                'password' => bcrypt($validasi['password']),
            ]);
        });

        return redirect()->route('admin.wadek')->with('success', 'Wakil Dekan berhasil ditambahkan.');
    }

    public function editWadek($id)
    {
        $user = User::findOrFail($id);
        $wadek = wadek::where('user_id', $id)->first();
        return view('users::kontrol_wadek.edit', compact('user', 'wadek'));
    }

    public function updateWadek(Request $request, $id)
    {
        $valdiasi =$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        DB::transaction(function () use ($valdiasi, $id) {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $valdiasi['name'],
                'email' => $valdiasi['email'],
            ]);

            $wadek = wadek::where('user_id', $id)->first();
            $wadek->update([
            ]);
        });

        return redirect()->route('admin.wadek')->with('success', 'Wakil Dekan berhasil diperbarui.');
    }

     public function destroyWadek($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            wadek::where('user_id', $id)->delete();
            $user->delete();
        });

        return redirect()->route('admin.wadek')->with('success', 'Wakil Dekan berhasil dihapus.');
    }






    /**
     * Display the specified resource.
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
