<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil fakultas dari DB, key = kode_fakultas (misal: FIP, FPIPS, dst)
        $fakultasByKode = DB::table('fakultas')
            ->select('id', 'kode_fakultas', 'nama_fakultas')
            ->get()
            ->keyBy(fn($f) => strtoupper(trim($f->kode_fakultas)));

        // Fallback map jika ternyata kode_fakultas di DB beda/ kosong
        $fakultasByNama = DB::table('fakultas')
            ->select('id', 'nama_fakultas')
            ->get()
            ->keyBy(fn($f) => strtolower(trim($f->nama_fakultas)));

        // Helper ambil fakultas_id dari kode atau nama
        $getFakultasId = function (string $kode, string $nama) use ($fakultasByKode) {
            $kodeKey = strtoupper(trim($kode));
            if (isset($fakultasByKode[$kodeKey])) return $fakultasByKode[$kodeKey]->id;

            // fallback: cari pakai LIKE (lebih fleksibel)
            $row = DB::table('fakultas')
                ->where('nama_fakultas', 'like', '%' . trim($nama) . '%')
                ->orWhere('nama_fakultas', 'like', '%' . preg_replace('/\s*\(.*\)$/', '', trim($nama)) . '%') // buang "(FIP)"
                ->first();

            return $row?->id;
        };

        $data = [
            // 1) FIP
            [
                'kode' => 'FIP',
                'nama_fakultas' => 'Fakultas Ilmu Pendidikan (FIP)',
                'prodi' => [
                    'Administrasi Pendidikan (S1)',
                    'Administrasi Pendidikan (S2)',
                    'Bimbingan dan Konseling (S1)',
                    'Bimbingan dan Konseling (S2)',
                    'Bimbingan dan Konseling (S3)',
                    'Pendidikan Anak Usia Dini (S1)',
                    'Pendidikan Anak Usia Dini (S2)',
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Khusus (S1)',
                    'Pendidikan Khusus (S2)',
                    'Pendidikan Khusus (S3)',
                    'Pendidikan Masyarakat (S1)',
                    'Pendidikan Masyarakat (S2)',
                    'Pendidikan Masyarakat (S3)',
                    'Perpustakaan dan Sains Informasi (S1)',
                    'Psikologi (S1)',
                    'Teknologi Pendidikan (S1)',
                    'Pedagogik (S2)',
                    'Pengembangan Kurikulum (S2)',
                    'Pengembangan Kurikulum (S3)',
                    'Administrasi Pendidikan (S3)',
                ],
            ],

            // 2) FPIPS
            [
                'kode' => 'FPIPS',
                'nama_fakultas' => 'Fakultas Pendidikan Ilmu Pengetahuan Sosial (FPIPS)',
                'prodi' => [
                    'Pendidikan Pancasila dan Kewarganegaraan (S1)',
                    'Pendidikan Sejarah (S1)',
                    'Pendidikan Sejarah (S2)',
                    'Pendidikan Sejarah (S3)',
                    'Pendidikan Geografi (S1)',
                    'Pendidikan Geografi (S2)',
                    'Pendidikan Geografi (S3)',
                    'Pendidikan Ilmu Pengetahuan Sosial (S1)',
                    'Pendidikan Ilmu Pengetahuan Sosial (S2)',
                    'Pendidikan Ilmu Pengetahuan Sosial (S3)',
                    'Ilmu Pendidikan Agama Islam (S1)',
                    'Manajemen Resort & Leisure (S1)',
                    'Manajemen Pemasaran Pariwisata (S1)',
                    'Manajemen Industri Katering (S1)',
                    'Pendidikan Sosiologi (S1)',
                    'Pendidikan Sosiologi (S2)',
                    'Pendidikan Sosiologi (S3)',
                    'Sains Informasi Geografi (S1)',
                    'Ilmu Komunikasi (S1)',
                    'Ilmu Hukum (S1)',
                    'Pendidikan Pariwisata (S1)',
                    'Survei Pemetaan dan Informasi Geografis (D4)',
                    'Pendidikan Agama Islam (S2)',
                    'Pendidikan Agama Islam (S3)',
                    'Pendidikan Kewarganegaraan (S2)',
                    'Pendidikan Kewarganegaraan (S3)',
                ],
            ],

            // 3) FPBS
            [
                'kode' => 'FPBS',
                'nama_fakultas' => 'Fakultas Pendidikan Bahasa dan Sastra (FPBS)',
                'prodi' => [
                    'Pendidikan Bahasa dan Sastra Indonesia (S1)',
                    'Pendidikan Bahasa dan Sastra Indonesia (S2)',
                    'Pendidikan Bahasa dan Sastra Indonesia (S3)',
                    'Bahasa dan Sastra Indonesia (S1)',
                    'Pendidikan Bahasa Inggris (S1)',
                    'Pendidikan Bahasa Inggris (S2)',
                    'Pendidikan Bahasa Inggris (S3)',
                    'Bahasa dan Sastra Inggris (S1)',
                    'Pendidikan Bahasa Arab (S1)',
                    'Pendidikan Bahasa Arab (S2)',
                    'Pendidikan Bahasa Jepang (S1)',
                    'Pendidikan Bahasa Jepang (S2)',
                    'Pendidikan Bahasa Jerman (S1)',
                    'Pendidikan Bahasa Perancis (S1)',
                    'Pendidikan Bahasa Korea (S1)',
                    'Pendidikan Bahasa Sunda (S1)',
                    'Pendidikan Bahasa Sunda (S2)',

                ],
            ],

            // 4) FPMIPA
            [
                'kode' => 'FPMIPA',
                'nama_fakultas' => 'Fakultas Pendidikan Matematika dan Ilmu Pengetahuan Alam (FPMIPA)',
                'prodi' => [
                    'Pendidikan Matematika (S1)',
                    'Pendidikan Matematika (S2)',
                    'Pendidikan Matematika (S3)',
                    'Matematika (S1)',
                    'Pendidikan Fisika (S1)',
                    'Pendidikan Fisika (S2)',
                    'Fisika (S1)',
                    'Pendidikan Biologi (S1)',
                    'Biologi (S1)',
                    'Pendidikan Kimia (S1)',
                    'Pendidikan Kimia (S2)',
                    'Kimia (S1)',
                    'Kimia (S2)',
                    'Pendidikan Ilmu Komputer (S1)',
                    'Pendidikan Ilmu Komputer (S2)',
                    'Ilmu Komputer (S1)',
                    'Pendidikan IPA (S1)',
                    'Pendidikan IPA (S2)',
                    'Pendidikan IPA (S3)',
                ],
            ],

            // 5) FPTK
            [
                'kode' => 'FPTK',
                'nama_fakultas' => 'Fakultas Pendidikan Teknologi dan Kejuruan (FPTK)',
                'prodi' => [
                    'Pendidikan Teknik Bangunan (S1)',
                    'Pendidikan Teknik Arsitektur (S1)',
                    'Arsitektur (S1)',
                    'Arsitektur (S2)',
                    'Pendidikan Teknik Elektro (S1)',
                    'Pendidikan Teknik Mesin (S1)',
                    'Pendidikan Teknik Otomotif (S1)',
                    'Pendidikan Teknologi Agroindustri (S1)',
                    'Pendidikan Teknik Otomasi Industri dan Robotika (S1)',
                    'Pendidikan Tata Boga (S1)',
                    'Pendidikan Tata Busana (S1)',
                    'Pendidikan Kesejahteraan Keluarga (S1)',
                    'Teknik Sipil (S1)',
                    'Teknik Sipil (D3)',
                    'Teknik Mesin (D3)',
                    'Teknik Elektro (D3)',
                    'Teknik Arsitektur Perumahan (D3)',
                    'Teknik Elektro (S1)',
                    'Teknik Arsitektur (S1)',
                    'Teknik Energi Terbarukan (S1)',
                    'Pendidikan Teknologi dan Kejuruan (S2)',

                ],
            ],

            // 6) FPOK
            [
                'kode' => 'FPOK',
                'nama_fakultas' => 'Fakultas Pendidikan Olahraga dan Kesehatan (FPOK)',
                'prodi' => [
                    'Pendidikan Jasmani, Kesehatan dan Rekreasi (PJKR) (S1)',
                    'Pendidikan Guru Sekolah Dasar Pendidikan Jasmani (S1)',
                    'Pendidikan Kepelatihan Olahraga (S1)',
                    'Ilmu Keolahragaan (S1)',
                    'Keperawatan (S1)',
                    'Keperawatan (D3)',
                    'Pendidikan Guru PAUD (S1)',
                    'PGSD Pendidikan Jasmani (S1)',
                    'Kepelatihan Fisik Olahraga (S1)',
                    'Gizi (S1)',
                    'Pendidikan Olahraga (S2)',
                    'Pendidikan Olahraga (S3)',
                ],
            ],

            // 7) FPEB
            [
                'kode' => 'FPEB',
                'nama_fakultas' => 'Fakultas Pendidikan Ekonomi dan Bisnis (FPEB)',
                'prodi' => [
                    'Pendidikan Ekonomi (S1)',
                    'Pendidikan Ekonomi (S2)',
                    'Pendidikan Ekonomi (S3)',
                    'Pendidikan Manajemen Perkantoran (S1)',
                    'Pendidikan Bisnis (S1)',
                    'Pendidikan Akuntansi (S1)',
                    'Pendidikan Tata Niaga (S1)',
                    'Manajemen (S1)',
                    'Manajemen (S2)',
                    'Manajemen (S3)',
                    'Akuntansi (S1)',
                    'Akuntansi (S2)',
                    'Ilmu Ekonomi dan Keuangan Islam (S1)',
                    'Ilmu Ekonomi (S2)',
                ],
            ],

            // 8) FPSD
            [
                'kode' => 'FPSD',
                'nama_fakultas' => 'Fakultas Pendidikan Seni dan Desain (FPSD)',
                'prodi' => [
                    'Pendidikan Seni Rupa (S1)',
                    'Pendidikan Seni Tari (S1)',
                    'Pendidikan Seni Musik (S1)',
                    'Musik (S1)',
                    'Desain Komunikasi Visual (S1)',
                    'Film dan Televisi (S1)',
                    'Pendidikan Seni (S2)',
                    'Pendidikan Seni (S3)',
                ],
            ],

            // 9) FK
            [
                'kode' => 'FK',
                'nama_fakultas' => 'Fakultas Kedokteran (FK)',
                'prodi' => [
                    'Kedokteran (S1)',
                    'Pendidikan Profesi Dokter (Profesi)',
                ],
            ],

            [
                'kode' => 'CIBIRU',
                'nama_fakultas' => 'Kampus Daerah CIBIRU',
                'prodi' => [
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Guru Sekolah Dasar (S2)',
                    'Pendidikan Anak Usia Dini (S1)',
                    'Pendidikan Multimedia (S1)',
                    'Rekayasa Perangkat Lunak (S1)',
                    'Teknik Komputer (S1)',
                ]

            ],

            [
                'kode' => 'SUMEDANG',
                'nama_fakultas' => 'Kampus Daerah Sumedang',
                'prodi' => [
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Jasmani Sekolah Dasar (S1)',
                    'Pendidikan Jasmani (S2)',
                    'Keperawatan (S1)',
                    'Industri Parawisata (S1)',
                ]
            ],

            [
                'kode' => 'TASIK',
                'nama_fakultas' => 'Kampus Daerah Tasikmalaya',
                'prodi' => [
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Anak Usia Dini (S1)',
                    'Kewirausahaan (S1)',
                    'Bisnis Digital (S1)',
                    'Desain Komunikasi Visual (S1)',
                    'Keperawatan (D3)',
                ]
            ],

            [
                'kode' => 'PURWAKARTA',
                'nama_fakultas' => 'Kampus Daerah Purwakarta',
                'prodi' => [
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Anak Usia Dini (S1)',
                    'Sistem dan Teknologi Informasi (S1)',
                    'Sistem Telekomunikasi (S1)',
                    'Sistem Mekatronika (S1)',
                ]
            ],

            [
                'kode' => 'SERANG',
                'nama_fakultas' => 'Kampus Daerah Serang',
                'prodi' => [
                    'Pendidikan Guru Sekolah Dasar (S1)',
                    'Pendidikan Anak Usia Dini (S1)',
                    'Kelautan dan Perikanan (S1)',
                    'Sistem Informasi Kelautan (S1)',
                    'Logistik Kelautan (S1)',
                ]
            ],

            // 10) SPs (Sekolah Pascasarjana)
            [
                'kode' => 'SPS',
                'nama_fakultas' => 'Sekolah Pascasarjana (SPs)',
                'prodi' => [
                    'Magister (S2) dan Doktor (S3) Pendidikan (Berbagai konsentrasi)',
                    'Linguistik (S2/S3)',
                    'Pendidikan Profesi Guru (PPG)',
                ],
            ],

        ];

        foreach ($data as $item) {
            $fakultasId = $getFakultasId($item['kode'], $item['nama_fakultas']);

            if (!$fakultasId) {
                // Skip kalau fakultas belum ada di tabel fakultas
                // (misal kode_fakultas beda)
                continue;
            }

            foreach ($item['prodi'] as $namaProdi) {
                // Hindari duplicate: unik per (fakultas_id, nama_prodi)
                DB::table('prodi')->updateOrInsert(
                    ['fakultas_id' => $fakultasId, 'nama_prodi' => $namaProdi],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
