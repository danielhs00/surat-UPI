<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fakultas')->insert([
            ['nama_fakultas' => 'Fakultas Bahasa', 'kode_fakultas' => 'FBS'],
            ['nama_fakultas' => 'Fakultas Teknik', 'kode_fakultas' => 'FT'],
            ['nama_fakultas' => 'Fakultas Mesin', 'kode_fakultas' => 'FM'],
        ]);
        // $this->call([]);
    }
}
