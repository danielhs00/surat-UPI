<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Operator;
use App\Models\Wadek;
use App\Models\Mahasiswa;
use Modules\Master\Models\Fakultas;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil fakultas pertama (Fakultas Bahasa)
        $fakultas = Fakultas::first();

        if (!$fakultas) {
            DB::table('fakultas')->insert([
                ['nama_fakultas' => 'Fakultas Bahasa', 'kode_fakultas' => 'FBS'],
                ['nama_fakultas' => 'Fakultas Teknik', 'kode_fakultas' => 'FT'],
                ['nama_fakultas' => 'Fakultas Mesin', 'kode_fakultas' => 'FM'],
            ]);

    $fakultas = Fakultas::first();
}

        // ======================
        // ADMIN
        // ======================
        User::create([
            'name' => 'Admin',
            'email' => 'admin@upi.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ======================
        // OPERATOR
        // ======================
        $operatorUser = User::create([
            'name' => 'Operator Bahasa',
            'email' => 'operator@upi.test',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'fakultas_id' => $fakultas->id,
        ]);

        Operator::create([
            'user_id' => $operatorUser->id,
            'fakultas_id' => $fakultas->id,
        ]);

        // ======================
        // WADEK
        // ======================
        $wadekUser = User::create([
            'name' => 'Wadek Bahasa',
            'email' => 'wadek@upi.test',
            'password' => Hash::make('password'),
            'role' => 'wadek',
            'fakultas_id' => $fakultas->id,
        ]);

        Wadek::create([
            'user_id' => $wadekUser->id,
            'fakultas_id' => $fakultas->id,
        ]);

        // ======================
        // MAHASISWA
        // ======================
        $mahasiswaUser = User::create([
            'name' => 'Mahasiswa Bahasa',
            'email' => 'mahasiswa@upi.test',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'fakultas_id' => $fakultas->id,
        ]);

        Mahasiswa::create([
            'user_id' => $mahasiswaUser->id,
            'fakultas_id' => $fakultas->id,
            'nim' => '12345678',
            'prodi' => 'Pendidikan Bahasa',
            'angkatan' => 2022,
        ]);
    }
}
