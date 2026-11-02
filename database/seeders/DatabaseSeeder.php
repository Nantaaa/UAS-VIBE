<?php

namespace Database\Seeders;

use App\Models\Destinasi;
use App\Models\JadwalKeberangkatan;
use App\Models\KodePromo;
use App\Models\PaketWisata;
use App\Models\Penugasan;
use App\Models\Petugas;
use App\Models\TitikJemput;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default
        User::create([
            'nama_lengkap' => 'Administrator Nanta',
            'email' => 'admin@nanta.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        User::create([
            'nama_lengkap' => 'Wisatawan Nusantara',
            'email' => 'wisatawan@nanta.id',
            'password' => Hash::make('password'),
            'role' => 'wisatawan',
            'no_hp' => '081298765432',
        ]);

        // 2. Petugas Kru
        $petugas1 = Petugas::create(['nama_petugas' => 'Bagus Prasetyo', 'jabatan' => 'Tour Guide', 'no_telepon' => '081211112222', 'status_aktif' => true]);
        $petugas2 = Petugas::create(['nama_petugas' => 'Rina Kusuma', 'jabatan' => 'Tour Leader', 'no_telepon' => '081233334444', 'status_aktif' => true]);
        $petugas3 = Petugas::create(['nama_petugas' => 'Sutrisno', 'jabatan' => 'Driver & Kru', 'no_telepon' => '081255556666', 'status_aktif' => true]);

        // 3. Destinasi
        $destinasiData = [
            ['nama_destinasi' => 'Labuan Bajo', 'lokasi' => 'Flores, Nusa Tenggara Timur', 'deskripsi' => 'Pintu gerbang menuju Taman Nasional Komodo, Pulau Padar, dan Pink Beach.', 'foto' => 'destinasi/komodo.jpg'],
            ['nama_destinasi' => 'Gunung Bromo', 'lokasi' => 'Probolinggo & Pasuruan, Jawa Timur', 'deskripsi' => 'Lautan pasir berbisik dan kawah aktif spektakuler di TN Bromo Tengger Semeru.', 'foto' => 'destinasi/bromo.jpg'],
            ['nama_destinasi' => 'Raja Ampat', 'lokasi' => 'Papua Barat Daya', 'deskripsi' => 'Surga bawah laut terindah di dunia dengan gugusan pulau karang karst ikonik.', 'foto' => 'destinasi/raja_ampat.jpg'],
            ['nama_destinasi' => 'Nusa Penida', 'lokasi' => 'Klungkung, Bali', 'deskripsi' => 'Tebing karst megah Kelingking Beach, Diamond Beach, dan perairan Manta Point.', 'foto' => 'destinasi/nusa_penida.jpg'],
            ['nama_destinasi' => 'Kepulauan Derawan', 'lokasi' => 'Berau, Kalimantan Timur', 'deskripsi' => 'Danau ubur-ubur tanpa sengat Kakaban, surga penyu raksasa Sangalaki & Maratua.', 'foto' => 'destinasi/derawan.jpg'],
            ['nama_destinasi' => 'Pulau Sumba', 'lokasi' => 'Nusa Tenggara Timur', 'deskripsi' => 'Desa adat Ratenggaro, Danau Weekuri, dan bentang savana liar Puru Kambera.', 'foto' => 'destinasi/sumba.jpg'],
            ['nama_destinasi' => 'Yogyakarta', 'lokasi' => 'DI Yogyakarta', 'deskripsi' => 'Pusat kebudayaan Jawa, kemegahan Candi Borobudur, Prambanan, dan Malioboro.', 'foto' => 'destinasi/yogyakarta.jpg'],
            ['nama_destinasi' => 'Lombok', 'lokasi' => 'Nusa Tenggara Barat', 'deskripsi' => 'Tiga gili eksotis Trawangan-Meno-Air dan keindahan pesisir Pantai Mandalika.', 'foto' => 'destinasi/lombok.jpg'],
            ['nama_destinasi' => 'Danau Toba', 'lokasi' => 'Sumatera Utara', 'deskripsi' => 'Danau vulkanik terbesar di dunia dengan kekayaan budaya Batak Pulau Samosir.', 'foto' => 'destinasi/danau_toba.jpg'],
            ['nama_destinasi' => 'Wakatobi', 'lokasi' => 'Sulawesi Tenggara', 'deskripsi' => 'Cagar biosfer dunia karang laut dengan kejernihan air spektakuler.', 'foto' => 'destinasi/wakatobi.jpg'],
            ['nama_destinasi' => 'Belitung', 'lokasi' => 'Tanjung Pandan, Bangka Belitung', 'deskripsi' => 'Gugusan batu granit putih eksotis dan mercusuar bersejarah Pulau Lengkuas.', 'foto' => 'destinasi/belitung.jpg'],
            ['nama_destinasi' => 'Dieng', 'lokasi' => 'Wonosobo, Jawa Tengah', 'deskripsi' => 'Negeri di atas awan, Golden Sunrise Bukit Sikunir, dan Kawah Sikidang.', 'foto' => 'destinasi/dieng.jpg'],
            ['nama_destinasi' => 'Karimunjawa', 'lokasi' => 'Jepara, Jawa Tengah', 'deskripsi' => 'Gugusan pulau tropis Laut Jawa, penangkaran hiu, dan pantai pasir putih.', 'foto' => 'destinasi/karimunjawa.jpg'],
            ['nama_destinasi' => 'Bunaken', 'lokasi' => 'Manado, Sulawesi Utara', 'deskripsi' => 'Wall reef vertikal legendaris dan suaka penyu raksasa Bunaken.', 'foto' => 'destinasi/bunaken.jpg'],
            ['nama_destinasi' => 'Kawah Ijen & Baluran', 'lokasi' => 'Banyuwangi, Jawa Timur', 'deskripsi' => 'Api biru Blue Fire Kawah Ijen dan savana Afrika van Java Baluran.', 'foto' => 'destinasi/kawah_ijen.jpg'],
            ['nama_destinasi' => 'Banda Neira', 'lokasi' => 'Kepulauan Banda, Maluku', 'deskripsi' => 'Nol kilometer jalur rempah pala dunia, Benteng Belgica, dan Lava Flow.', 'foto' => 'destinasi/banda_neira.jpg'],
            ['nama_destinasi' => 'Tana Toraja', 'lokasi' => 'Sulawesi Selatan', 'deskripsi' => 'Desa adat Tongkonan Kete Kesu, makam tebing batu Lemo, dan ritual adat.', 'foto' => 'destinasi/toraja.jpg'],
            ['nama_destinasi' => 'Kepulauan Seribu', 'lokasi' => 'DKI Jakarta', 'deskripsi' => 'Island hopping bahari Pulau Harapan, Pulau Perak, dan terumbu karang.', 'foto' => 'destinasi/pulau_seribu.jpg'],
            ['nama_destinasi' => 'Padang & Bukittinggi', 'lokasi' => 'Sumatera Barat', 'deskripsi' => 'Tebing megah Lembah Harau, Ngarai Sianok, dan kuliner Minang.', 'foto' => 'destinasi/padang.jpg'],
            ['nama_destinasi' => 'Bali', 'lokasi' => 'Denpasar & Nusa Dua, Bali', 'deskripsi' => 'Resor mewah, sunset dinner pantai, dan vila privat romantis.', 'foto' => 'destinasi/bali_resort.jpg'],
        ];

        $destMap = [];
        foreach ($destinasiData as $d) {
            $dest = Destinasi::create($d);
            $destMap[$d['nama_destinasi']] = $dest->id;
        }

        // 4. Promo
        KodePromo::create([
            'kode' => 'HEMAT10',
            'tipe' => 'persentase',
            'nilai' => 10,
            'min_transaksi' => 1000000,
            'maks_diskon' => 500000,
            'kuota' => 100,
            'aktif' => true,
        ]);
        KodePromo::create([
            'kode' => 'LIBURAN200',
            'tipe' => 'nominal',
            'nilai' => 200000,
            'min_transaksi' => 2000000,
            'kuota' => 50,
            'aktif' => true,
        ]);
    }
}
