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
            'nama_lengkap' => 'Budi Pratama',
            'email' => 'admin@nanta.id',
            'password_hash' => 'password',
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        User::create([
            'nama_lengkap' => 'Budi Pratama',
            'email' => 'wisatawan@nanta.id',
            'password_hash' => 'password',
            'role' => 'wisatawan',
            'no_hp' => '081298765432',
        ]);

        $destinasi = [
            ['nama_destinasi' => 'Bali', 'lokasi' => 'Denpasar, Bali', 'deskripsi' => 'Pulau dewata dengan pantai, pura, dan sawah terasering yang ikonik.'],
            ['nama_destinasi' => 'Yogyakarta', 'lokasi' => 'DI Yogyakarta', 'deskripsi' => 'Kota budaya dengan candi Borobudur, Prambanan, dan Malioboro.'],
            ['nama_destinasi' => 'Lombok', 'lokasi' => 'Nusa Tenggara Barat', 'deskripsi' => 'Gili Trawangan, Rinjani, dan pantai Kuta Mandalika.'],
            ['nama_destinasi' => 'Labuan Bajo', 'lokasi' => 'Nusa Tenggara Timur', 'deskripsi' => 'Gerbang menuju Taman Nasional Komodo dan Pulau Padar.'],
            ['nama_destinasi' => 'Raja Ampat', 'lokasi' => 'Papua Barat', 'deskripsi' => 'Surga bawah laut dengan keanekaragaman terumbu karang tertinggi.'],
            ['nama_destinasi' => 'Danau Toba', 'lokasi' => 'Sumatera Utara', 'deskripsi' => 'Danau vulkanik terbesar di dunia dengan Pulau Samosir.'],
            ['nama_destinasi' => 'Bromo Tengger', 'lokasi' => 'Malang, Jawa Timur', 'deskripsi' => 'Lautan pasir dan matahari terbit di kawah gunung Bromo.'],
            ['nama_destinasi' => 'Derawan', 'lokasi' => 'Berau, Kalimantan Timur', 'deskripsi' => 'Kepulauan Derawan, Maratua, dan Danau Ubur-ubur Kakaban.'],
            ['nama_destinasi' => 'Sumba', 'lokasi' => 'Nusa Tenggara Timur', 'deskripsi' => 'Desa adat Wae Rebo, savana, dan pantai liar Sumba.'],
        ];

        foreach ($destinasi as $i => $d) {
            $d['foto'] = 'destinasi/dest-'.($i + 1).'.jpg';
            Destinasi::create($d);
        }

        $paket = [
            [
                'id_destinasi' => 4, 'nama_paket' => 'Eksplorasi Komodo & Labuan Bajo 3H2M', 'durasi_hari' => 3,
                'tipe_trip' => 'Open Trip', 'tarif' => 4850000, 'harga_asli' => 5500000, 'is_bestseller' => true,
                'highlight' => 'Open Trip • Snorkeling • Sunset',
                'deskripsi' => 'Live on board phinisi menuju Padar, Pink Beach, dan Taman Nasional Komodo.',
                'fasilitas' => "Kapal phinisi (2 malam)\nFull board meal\nAlat snorkeling\nGuide & ranger\nTiket TN Komodo",
                'itinerary' => "Hari 1: Labuan Bajo - Kelor - Kalong\nHari 2: Padar - Pink Beach - Komodo\nHari 3: Manta Point - Labuan Bajo",
            ],
            [
                'id_destinasi' => 7, 'nama_paket' => 'Pesona Sunrise Bromo & Madakaripura 3H2M', 'durasi_hari' => 3,
                'tipe_trip' => 'Open Trip', 'tarif' => 1250000, 'harga_asli' => 1500000, 'is_bestseller' => true,
                'highlight' => '3 Hari 2 Malam • Jeep',
                'deskripsi' => 'Sunrise Penanjakan, kawah Bromo, dan air terjun Madakaripura.',
                'fasilitas' => "Jeep 4x4\nHomestay 2 malam\nTiket masuk\nGuide\nMakan 4x",
                'itinerary' => "Hari 1: Malang - homestay\nHari 2: Sunrise Penanjakan - kawah Bromo\nHari 3: Madakaripura - pulang",
            ],
            [
                'id_destinasi' => 5, 'nama_paket' => 'Exotic Raja Ampat Pesona Piaynemo & Misool 5H4M', 'durasi_hari' => 5,
                'tipe_trip' => 'Private Trip', 'tarif' => 6900000, 'harga_asli' => 7500000, 'is_bestseller' => true,
                'highlight' => '5 Hari 4 Malam • Speedboat',
                'deskripsi' => 'Menjelajah Piaynemo, Misool, Arborek, dan melihat cendrawasih.',
                'fasilitas' => "Resort (4 malam)\nFull board\nBoat trip\nAlat snorkeling\nGuide lokal\nRetribusi",
                'itinerary' => "Hari 1: Sorong - Waisai\nHari 2: Piaynemo\nHari 3: Arborek & Manta\nHari 4: Misool\nHari 5: Kembali",
            ],
            [
                'id_destinasi' => 1, 'nama_paket' => 'Romantic Nusa Penida Island Escape 3H2M', 'durasi_hari' => 3,
                'tipe_trip' => 'Private Trip', 'tarif' => 2450000, 'harga_asli' => 2900000, 'is_bestseller' => false,
                'highlight' => '3 Hari 2 Malam • Private',
                'deskripsi' => 'Kelingking Beach, Diamond Beach, dan snorkeling di Manta Point.',
                'fasilitas' => "Hotel 2 malam\nTransport private\nFast boat\nSnorkeling\nGuide",
                'itinerary' => "Hari 1: Sanur - Nusa Penida\nHari 2: Kelingking - Diamond Beach\nHari 3: Manta Point - pulang",
            ],
            [
                'id_destinasi' => 8, 'nama_paket' => 'Derawan, Maratua & Kakaban Jellyfish Lake 4H3M', 'durasi_hari' => 4,
                'tipe_trip' => 'Open Trip', 'tarif' => 3850000, 'harga_asli' => 4500000, 'is_bestseller' => false,
                'highlight' => '4 Hari 3 Malam • Snorkeling',
                'deskripsi' => 'Berenang bersama ubur-ubur tanpa sengat di Danau Kakaban.',
                'fasilitas' => "Resort 3 malam\nFull board\nSpeedboat\nSnorkeling\nGuide",
                'itinerary' => "Hari 1: Berau - Derawan\nHari 2: Kakaban - Maratua\nHari 3: Sangalaki\nHari 4: Kembali",
            ],
            [
                'id_destinasi' => 9, 'nama_paket' => 'Eksotisme Sumba Cultural Heritage 4H3M', 'durasi_hari' => 4,
                'tipe_trip' => 'Cultural', 'tarif' => 4200000, 'harga_asli' => 4800000, 'is_bestseller' => false,
                'highlight' => '4 Hari 3 Malam • Cultural',
                'deskripsi' => 'Desa adat Ratenggaro, Pantai Nihiwatu, dan savana Sumba.',
                'fasilitas' => "Hotel 3 malam\nTransport AC\nMakan 6x\nGuide budaya",
                'itinerary' => "Hari 1: Tambolaka - Ratenggaro\nHari 2: Weekuri - Nihiwatu\nHari 3: Savana Puru Kambera\nHari 4: Kembali",
            ],
            [
                'id_destinasi' => 1, 'nama_paket' => 'Bali Honeymoon 4D3N', 'durasi_hari' => 4,
                'tipe_trip' => 'Honeymoon', 'tarif' => 3250000, 'harga_asli' => 3800000, 'is_bestseller' => false,
                'highlight' => '4 Hari 3 Malam • Private',
                'deskripsi' => 'Paket bulan madu dengan dinner romantis di tepi pantai Jimbaran.',
                'fasilitas' => "Hotel bintang 4 (3 malam)\nTransport private\nDinner romantis\nSpa couple\nDekorasi kamar",
                'itinerary' => "Hari 1: Kedatangan, check-in\nHari 2: Nusa Penida tour\nHari 3: Ubud & spa\nHari 4: Jimbaran dinner, pulang",
            ],
            [
                'id_destinasi' => 2, 'nama_paket' => 'Jogja Heritage 2D1N', 'durasi_hari' => 2,
                'tipe_trip' => 'Open Trip', 'tarif' => 750000, 'harga_asli' => 900000, 'is_bestseller' => false,
                'highlight' => '2 Hari 1 Malam • Budaya',
                'deskripsi' => 'Borobudur sunrise, Prambanan, dan Malioboro dalam dua hari.',
                'fasilitas' => "Hotel 1 malam\nTransport AC\nMakan 3x\nTiket Borobudur & Prambanan\nGuide",
                'itinerary' => "Hari 1: Borobudur, Malioboro\nHari 2: Prambanan, keraton, pulang",
            ],
            [
                'id_destinasi' => 3, 'nama_paket' => 'Lombok Gili Escape 3D2N', 'durasi_hari' => 3,
                'tipe_trip' => 'Open Trip', 'tarif' => 1650000, 'harga_asli' => 1950000, 'is_bestseller' => false,
                'highlight' => '3 Hari 2 Malam • Island Hopping',
                'deskripsi' => 'Gili Trawangan, Gili Meno, dan Air Terjun Sendang Gile.',
                'fasilitas' => "Hotel 2 malam\nBoat trip\nMakan 4x\nGuide",
                'itinerary' => "Hari 1: Mataram - Gili Trawangan\nHari 2: Gili Meno - snorkeling\nHari 3: Sendang Gile - pulang",
            ],
            [
                'id_destinasi' => 6, 'nama_paket' => 'Danau Toba & Samosir 3D2N', 'durasi_hari' => 3,
                'tipe_trip' => 'Family', 'tarif' => 1350000, 'harga_asli' => 1600000, 'is_bestseller' => false,
                'highlight' => '3 Hari 2 Malam • Keluarga',
                'deskripsi' => 'Danau Toba, Pulau Samosir, dan Desa Tomok bersama keluarga.',
                'fasilitas' => "Hotel 2 malam\nTransport\nMakan 5x\nGuide",
                'itinerary' => "Hari 1: Medan - Parapat\nHari 2: Samosir - Tomok\nHari 3: Berastagi - pulang",
            ],
        ];

        foreach ($paket as $i => $p) {
            $p['foto'] = 'paket/paket-'.($i + 1).'.jpg';
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
