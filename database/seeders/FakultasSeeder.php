<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_fakultas' => 'Fakultas Ilmu Pendidikan', 'kode_fakultas' => 'FIP'],
            ['nama_fakultas' => 'Fakultas Pendidikan Ilmu Pengetahuan Sosial', 'kode_fakultas' => 'FPIPS'],
            ['nama_fakultas' => 'Fakultas Pendidikan Bahasa dan Sastra', 'kode_fakultas' => 'FPBS'],
            ['nama_fakultas' => 'Fakultas Pendidikan Matematika dan Ilmu Pengetahuan Alam', 'kode_fakultas' => 'FPMIPA'],
            ['nama_fakultas' => 'Fakultas Pendidikan Teknologi dan Kejuruan', 'kode_fakultas' => 'FPTK'],
            ['nama_fakultas' => 'Fakultas Pendidikan Olahraga dan Kesehatan', 'kode_fakultas' => 'FPOK'],
            ['nama_fakultas' => 'Fakultas Pendidikan Ekonomi dan Bisnis', 'kode_fakultas' => 'FPEB'],
            ['nama_fakultas' => 'Fakultas Pendidikan Seni dan Desain', 'kode_fakultas' => 'FPSD'],
            ['nama_fakultas' => 'Fakultas Kedokteran', 'kode_fakultas' => 'FK'],
            ['nama_fakultas' => 'Kampus Daerah Cibiru', 'kode_fakultas' => 'CIBIRU'],
            ['nama_fakultas' => 'Kampus Daerah Sumedang', 'kode_fakultas' => 'SUMEDANG'],
            ['nama_fakultas' => 'Kampus Daerah Tasikmalaya', 'kode_fakultas' => 'TASIK'],
            ['nama_fakultas' => 'Kampus Daerah Purwakarta', 'kode_fakultas' => 'PURWAKARTA'],
            ['nama_fakultas' => 'Kampus Daerah Serang', 'kode_fakultas' => 'SERANG'],
            ['nama_fakultas' => 'Sekolah Pascasarjana', 'kode_fakultas' => 'SPS'],
        ];

        foreach ($data as $item) {
            DB::table('fakultas')->updateOrInsert(
                ['kode_fakultas' => $item['kode_fakultas']],
                [
                    'nama_fakultas' => $item['nama_fakultas'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}