<?php

namespace Database\Seeders;

use App\Models\Destinasi;
use App\Models\JadwalKeberangkatan;
use App\Models\PaketWisata;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Admin Travel',
            'email' => 'admin@javatrip.test',
            'password_hash' => 'password',
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        User::create([
            'nama_lengkap' => 'Ananta Mahendra',
            'email' => 'wisatawan@javatrip.test',
            'password_hash' => 'password',
            'role' => 'wisatawan',
            'no_hp' => '081298765432',
        ]);

        $destinasi = [
            ['nama_destinasi' => 'Bali', 'lokasi' => 'Denpasar, Bali', 'deskripsi' => 'Pulau dewata dengan pantai, pura, dan sawah terasering yang ikonik.'],
            ['nama_destinasi' => 'Yogyakarta', 'lokasi' => 'DI Yogyakarta', 'deskripsi' => 'Kota budaya dengan candi Borobudur, Prambanan, dan Malioboro.'],
            ['nama_destinasi' => 'Labuan Bajo', 'lokasi' => 'Nusa Tenggara Timur', 'deskripsi' => 'Gerbang menuju Taman Nasional Komodo dan Pulau Padar.'],
            ['nama_destinasi' => 'Bromo Tengger', 'lokasi' => 'Jawa Timur', 'deskripsi' => 'Lautan pasir dan matahari terbit di kawah gunung Bromo.'],
            ['nama_destinasi' => 'Raja Ampat', 'lokasi' => 'Papua Barat', 'deskripsi' => 'Surga bawah laut dengan keanekaragaman terumbu karang tertinggi.'],
            ['nama_destinasi' => 'Belitung', 'lokasi' => 'Bangka Belitung', 'deskripsi' => 'Pantai granit ikonik Tanjung Tinggi dan pulau-pulau kecil berpasir putih.'],
        ];

        foreach ($destinasi as $d) {
            Destinasi::create($d);
        }

        $paket = [
            [
                'id_destinasi' => 1, 'nama_paket' => 'Bali 3D2N Pesona Ubud & Kuta', 'durasi_hari' => 3,
                'tarif' => 1850000,
                'deskripsi' => 'Menjelajah Ubud, sawah Tegalalang, Pura Tanah Lot, dan bersantai di Pantai Kuta.',
                'fasilitas' => "Hotel bintang 3 (2 malam)\nTransport AC\nMakan 5x\nTiket masuk objek wisata\nGuide lokal",
                'itinerary' => "Hari 1: Penjemputan bandara, Ubud, Tegalalang\nHari 2: Tanah Lot, Pantai Kuta\nHari 3: Belanja oleh-oleh, transfer bandara",
            ],
            [
                'id_destinasi' => 1, 'nama_paket' => 'Bali Honeymoon 4D3N', 'durasi_hari' => 4,
                'tarif' => 3250000,
                'deskripsi' => 'Paket bulan madu dengan dinner romantis di tepi pantai Jimbaran.',
                'fasilitas' => "Hotel bintang 4 (3 malam)\nTransport private\nDinner romantis\nSpa couple\nDekorasi kamar",
                'itinerary' => "Hari 1: Kedatangan, check-in\nHari 2: Nusa Penida tour\nHari 3: Ubud & spa\nHari 4: Jimbaran dinner, pulang",
            ],
            [
                'id_destinasi' => 2, 'nama_paket' => 'Jogja Heritage 2D1N', 'durasi_hari' => 2,
                'tarif' => 750000,
                'deskripsi' => 'Borobudur sunrise, Prambanan, dan Malioboro dalam dua hari.',
                'fasilitas' => "Hotel 1 malam\nTransport AC\nMakan 3x\nTiket Borobudur & Prambanan\nGuide",
                'itinerary' => "Hari 1: Borobudur, Malioboro\nHari 2: Prambanan, keraton, pulang",
            ],
            [
                'id_destinasi' => 3, 'nama_paket' => 'Sailing Komodo 3D2N', 'durasi_hari' => 3,
                'tarif' => 2750000,
                'deskripsi' => 'Live on board menuju Padar, Pink Beach, dan Komodo.',
                'fasilitas' => "Kapal phinisi (2 malam)\nFull board meal\nAlat snorkeling\nGuide & ranger\nTiket TN Komodo",
                'itinerary' => "Hari 1: Labuan Bajo - Kelor - Kalong\nHari 2: Padar - Pink Beach - Komodo\nHari 3: Manta Point - Labuan Bajo",
            ],
            [
                'id_destinasi' => 4, 'nama_paket' => 'Bromo Midnight Trip', 'durasi_hari' => 2,
                'tarif' => 620000,
                'deskripsi' => 'Berangkat tengah malam mengejar sunrise Penanjakan dan kawah Bromo.',
                'fasilitas' => "Jeep 4x4\nHomestay 1 malam\nTiket masuk\nGuide\nMakan 2x",
                'itinerary' => "Hari 1: Penjemputan Malang, homestay\nHari 2: Sunrise Penanjakan, kawah Bromo, pulang",
            ],
            [
                'id_destinasi' => 5, 'nama_paket' => 'Raja Ampat Explorer 5D4N', 'durasi_hari' => 5,
                'tarif' => 6800000,
                'deskripsi' => 'Menyelam di Pianemo, Arborek, dan melihat cendrawasih.',
                'fasilitas' => "Resort (4 malam)\nFull board\nBoat trip\nAlat snorkeling\nGuide lokal\nRetribusi",
                'itinerary' => "Hari 1: Sorong - Waisai\nHari 2: Pianemo\nHari 3: Arborek & Manta\nHari 4: Cendrawasih\nHari 5: Kembali",
            ],
            [
                'id_destinasi' => 6, 'nama_paket' => 'Belitung Island Hoping 3D2N', 'durasi_hari' => 3,
                'tarif' => 1450000,
                'deskripsi' => 'Tanjung Tinggi, Pulau Lengkuas, dan snorkeling di kepayang.',
                'fasilitas' => "Hotel 2 malam\nTransport\nBoat trip\nMakan 5x\nTiket objek",
                'itinerary' => "Hari 1: Kedatangan, Tanjung Tinggi\nHari 2: Island hoping\nHari 3: Belanja, pulang",
            ],
        ];

        foreach ($paket as $p) {
            $model = PaketWisata::create($p);

            foreach ([1, 2, 3] as $offset) {
                $berangkat = now()->addWeeks($offset * 2)->startOfDay();

                JadwalKeberangkatan::create([
                    'id_paket' => $model->id,
                    'tanggal_berangkat' => $berangkat,
                    'tanggal_kembali' => $berangkat->copy()->addDays($model->durasi_hari - 1),
                    'kuota_peserta' => 20,
                    'sisa_kuota' => 20,
                ]);
            }
        }

        foreach ([
            ['nama_petugas' => 'Bagus Prasetyo', 'kontak' => '081211112222', 'peran' => 'guide'],
            ['nama_petugas' => 'Rina Wulandari', 'kontak' => '081233334444', 'peran' => 'guide'],
            ['nama_petugas' => 'Sutrisno', 'kontak' => '081255556666', 'peran' => 'driver'],
        ] as $petugas) {
            Petugas::create($petugas);
        }
    }
}
