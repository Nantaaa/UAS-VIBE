<?php

use App\Models\Destinasi;
use App\Models\PaketWisata;
use App\Models\JadwalKeberangkatan;
use App\Models\TitikJemput;
use App\Models\Penugasan;
use App\Models\Petugas;

$newTrips = [
    [
        'destinasi' => [
            'nama_destinasi' => 'Belitung',
            'lokasi' => 'Tanjung Pandan, Bangka Belitung',
            'deskripsi' => 'Gugusan pulau granit eksotis, mercusuar Pulau Lengkuas, dan pantai berpasir putih Laskar Pelangi.'
        ],
        'paket' => [
            'nama_paket' => 'Belitung Island Hopping & Pantai Laskar Pelangi 3H2M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 3,
            'tarif' => 1450000,
            'harga_asli' => 1750000,
            'is_bestseller' => true,
            'highlight' => 'Open Trip • Island Hopping • Granit',
            'deskripsi' => 'Eksplorasi Pulau Lengkuas, snorkeling bintang laut, Danau Kaolin, dan replika SD Muhammadiyah Laskar Pelangi.',
            'fasilitas' => "Hotel bintang 3 (2 malam)\nKapal boat tradisional island hopping\nAlat snorkeling & life jacket\nTransportasi darat AC\nMakan 5x + Mie Belitung khas",
            'termasuk' => "Akomodasi hotel 2 malam\nSewa kapal boat snorkeling\nTiket masuk objek wisata\nGuide lokal bersertifikat\nDokumentasi underwater",
            'tidak_termasuk' => "Tiket pesawat PP ke Tanjung Pandan\nPengeluaran pribadi & oleh-oleh\nTips kru sukarela",
            'itinerary' => "Hari 1: Bandara Tanjung Pandan - Danau Kaolin - Pantai Tanjung Tinggi (Laskar Pelangi) - Sunset Tanjung Kelayang\nHari 2: Island Hopping Pulau Lengkuas (Mercusuar 1882) - Pulau Pasir (Bintang Laut) - Pulau Batu Berlayar - Pulau Kelayang\nHari 3: Belanja oleh-oleh khas Belitung - Kopi Manggar - Transfer Bandara",
        ],
        'kota_kumpul' => 'Tanjung Pandan',
        'titik_kumpul' => 'Bandara H.A.S. Hanandjoeddin (TJQ) Tanjung Pandan',
        'jam_kumpul' => '09:30',
        'titik_jemput' => [
            ['nama' => 'Bandara H.A.S. Hanandjoeddin (TJQ) — gerbang kedatangan', 'jam' => '09:30', 'biaya_tambahan' => 0],
            ['nama' => 'Hotel area Tanjung Pandan kota (antar-jemput)', 'jam' => '10:15', 'biaya_tambahan' => 0],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Dieng',
            'lokasi' => 'Wonosobo, Jawa Tengah',
            'deskripsi' => 'Negeri di atas awan dengan keindahan golden sunrise Bukit Sikunir, kawah belerang, dan komplek candi Hindu tertua.'
        ],
        'paket' => [
            'nama_paket' => 'Eksotika Dieng Golden Sunrise Sikunir & Kawah Sikidang 2H1M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 2,
            'tarif' => 650000,
            'harga_asli' => 800000,
            'is_bestseller' => false,
            'highlight' => 'Open Trip • Golden Sunrise • Candi',
            'deskripsi' => 'Menikmati matahari terbit keemasan di Sikunir, kabut Telaga Warna, Kawah Sikidang, dan kehangatan kuliner Mie Ongklok.',
            'fasilitas' => "Homestay air hangat di Dieng (1 malam)\nTransport AC PP dari meeting point\nTiket masuk seluruh objek\nGuide lokal Dieng\nMakan 3x",
            'termasuk' => "Penginapan homestay 1 malam\nTransportasi microbus / minibus AC\nTiket masuk Sikunir, Sikidang, Telaga Warna, Candi Arjuna\nTour leader & guide lokal",
            'tidak_termasuk' => "Transportasi ke meeting point\nPengeluaran pribadi\nJaket hangat / sarung tangan",
            'itinerary' => "Hari 1: Kumpul di meeting point - Perjalanan ke Dieng - Gardu Pandang Tieng - Kompleks Candi Arjuna - Kawah Sikidang - Check-in homestay\nHari 2: Dini hari trekking Bukit Sikunir (Golden Sunrise) - Telaga Warna & Pengilon - Wisata Belanja carica - Perjalanan pulang",
        ],
        'kota_kumpul' => 'Wonosobo',
        'titik_kumpul' => 'Stasiun Purwokerto / Terminal Mendolo Wonosobo',
        'jam_kumpul' => '07:30',
        'titik_jemput' => [
            ['nama' => 'Stasiun Purwokerto — pintu keluar barat', 'jam' => '07:30', 'biaya_tambahan' => 0],
            ['nama' => 'Terminal Mendolo Wonosobo — lobby utama', 'jam' => '09:00', 'biaya_tambahan' => 0],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Karimunjawa',
            'lokasi' => 'Jepara, Jawa Tengah',
            'deskripsi' => 'Gugusan pulau tropis Laut Jawa dengan terumbu karang terjaga, penangkaran hiu, dan pantai pasir putih jernih.'
        ],
        'paket' => [
            'nama_paket' => 'Surga Bahari Karimunjawa Snorkeling & Shark Point 3H2M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 3,
            'tarif' => 1150000,
            'harga_asli' => 1350000,
            'is_bestseller' => true,
            'highlight' => 'Open Trip • Snorkeling • Hiu',
            'deskripsi' => 'Berenang bersama hiu jinak di Menjangan Besar, snorkeling karang Menjangan Kecil, dan sunset magis di Pantai Ujung Gelam.',
            'fasilitas' => "Tiket kapal express ferry PP Jepara-Karimun\nHomestay AC (2 malam)\nKapal wisata snorkeling keliling pulau\nAlat snorkeling lengkap & pelampung\nMakan 6x + BBQ ikan bakar di pulau",
            'termasuk' => "Tiket penyeberangan kapal cepat PP\nAkomodasi 2 malam ber-AC\nKapal boat tour laut 2 hari\nTiket retribusi Taman Nasional Karimunjawa\nFoto underwater (Gopro)",
            'tidak_termasuk' => "Transportasi ke Pelabuhan Kartini Jepara\nPengeluaran pribadi di luar paket\nSewa fin / kaki katak",
            'itinerary' => "Hari 1: Penyeberangan dari Jepara ke Karimunjawa - Check-in homestay - Sunset Bukit Cinta\nHari 2: Snorkeling Menjangan Kecil - Makan siang BBQ ikan di Pulau Cemara Kecil - Snorkeling Karang Bintang - Penangkaran Hiu Menjangan Besar - Sunset Pantai Ujung Gelam\nHari 3: Belanja suvenir kayu stigi - Penyeberangan kembali ke Pelabuhan Jepara",
        ],
        'kota_kumpul' => 'Jepara',
        'titik_kumpul' => 'Pelabuhan Kartini Jepara — ruang tunggu Express Bahari',
        'jam_kumpul' => '08:00',
        'titik_jemput' => [
            ['nama' => 'Pelabuhan Kartini Jepara — ruang tunggu kapal cepat', 'jam' => '08:00', 'biaya_tambahan' => 0],
            ['nama' => 'Stasiun Tawang Semarang (shuttle tambahan)', 'jam' => '05:30', 'biaya_tambahan' => 75000],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Bunaken',
            'lokasi' => 'Manado, Sulawesi Utara',
            'deskripsi' => 'Taman Nasional Laut Bunaken dengan dinding terumbu karang vertikal spektakuler, rumah bagi penyu raksasa.'
        ],
        'paket' => [
            'nama_paket' => 'Keajaiban Bawah Laut Bunaken & Pulau Siladen 3H2M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 3,
            'tarif' => 2250000,
            'harga_asli' => 2600000,
            'is_bestseller' => false,
            'highlight' => 'Open Trip • Penyu Raksasa • Siladen',
            'deskripsi' => 'Snorkeling di wall reef Bunaken bersama penyu raksasa, menikmati pasir putih Pulau Siladen, dan kuliner khas Manado.',
            'fasilitas' => "Hotel resort tepi pantai (2 malam)\nSpeedboat carter Bunaken & Siladen\nAlat snorkeling lengkap\nRetribusi TN Bunaken\nMakan 6x hidangan laut khas",
            'termasuk' => "Penginapan 2 malam\nSpeedboat tour pulau\nGuide selam / ranger laut\nKarcis masuk Taman Nasional\nAsuransi perjalanan",
            'tidak_termasuk' => "Tiket pesawat ke Manado\nSewa kamera underwater pribadi\nTips sukarela",
            'itinerary' => "Hari 1: Penjemputan di Manado - Menuju Dermaga Marina - Menyeberang ke Bunaken - Check-in resort - Sunset Siladen\nHari 2: Snorkeling Poin Lekuan 1, 2, 3 (Wall reef & penyu) - Makan siang di pulau - Snorkeling Tanjung Parigi - Kuliner Tinutuan & Ikan Woku\nHari 3: Hunting oleh-oleh klappertaart khas Manado - Transfer Bandara Sam Ratulangi",
        ],
        'kota_kumpul' => 'Manado',
        'titik_kumpul' => 'Bandara Sam Ratulangi (MDC) Manado',
        'jam_kumpul' => '09:00',
        'titik_jemput' => [
            ['nama' => 'Bandara Sam Ratulangi (MDC) — gerbang kedatangan', 'jam' => '09:00', 'biaya_tambahan' => 0],
            ['nama' => 'Dermaga Marina Plaza Manado', 'jam' => '10:00', 'biaya_tambahan' => 0],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Bali',
            'lokasi' => 'Denpasar & Nusa Dua, Bali',
            'deskripsi' => 'Pulau dewata dengan pantai privat, sunset cruise, dan vila romantis kelas dunia.'
        ],
        'paket' => [
            'nama_paket' => 'Bali Romantic Sunset Cruise & Luxury Villa 4H3M',
            'tipe_trip' => 'Honeymoon',
            'min_peserta' => 2,
            'durasi_hari' => 4,
            'tarif' => 3850000,
            'harga_asli' => 4500000,
            'is_bestseller' => false,
            'highlight' => 'Honeymoon • Private Villa • Cruise',
            'deskripsi' => 'Pengalaman bulan madu eksklusif dengan privat vila private pool, dinner sunset cruise kapal pesiar, dan spa pasangan di Jimbaran.',
            'fasilitas' => "Private Pool Villa 3 malam\nMobil privat Alphard/Innova + driver pribadi\nTiket Catamaran Sunset Dinner Cruise\nPaket Spa Pasangan 2 Jam\nMakan romantis di tepi pantai",
            'termasuk' => "Vila private pool 3 malam\nTransportasi privat 24 jam\nDekorasi bunga honeymoon di kamar\nCandlelight dinner di Jimbaran\nTiket cruise Nusa Lembongan",
            'tidak_termasuk' => "Tiket pesawat PP Bali\nPengeluaran belanja pribadi",
            'itinerary' => "Hari 1: Penjemputan VIP Bandara Ngurah Rai - Check-in vila Seminyak - Romantic dinner Jimbaran\nHari 2: Sunset cruise ke Nusa Lembongan - Watersport - Candlelight dinner di atas kapal\nHari 3: Ubud cultural walk - Spa treatment couple 2 jam - Fine dining Kintamani\nHari 4: Foto sesi pantai Melasti - Belanja oleh-oleh Krisna - Transfer Bandara",
        ],
        'kota_kumpul' => 'Denpasar',
        'titik_kumpul' => 'Bandara Internasional I Gusti Ngurah Rai (DPS)',
        'jam_kumpul' => '11:00',
        'titik_jemput' => [
            ['nama' => 'Bandara Ngurah Rai (DPS) — Pick up zone domestik', 'jam' => '11:00', 'biaya_tambahan' => 0],
            ['nama' => 'Lobby Hotel area Kuta / Seminyak', 'jam' => '11:45', 'biaya_tambahan' => 0],
        ]
    ],
];

foreach ($newTrips as $t) {
    // Cari atau buat destinasi
    $dest = Destinasi::firstOrCreate(
        ['nama_destinasi' => $t['destinasi']['nama_destinasi']],
        [
            'lokasi' => $t['destinasi']['lokasi'],
            'deskripsi' => $t['destinasi']['deskripsi'],
            'foto' => 'destinasi/dest-1.jpg',
        ]
    );

    // Buat paket wisata
    $pData = $t['paket'];
    $pData['id_destinasi'] = $dest->id;
    $pData['foto'] = 'paket/paket-1.jpg';
    $pData['is_active'] = true;

    $paket = PaketWisata::create($pData);

    // Buat 3 jadwal keberangkatan mendatang
    foreach ([2, 4, 6] as $minggu) {
        $berangkat = now()->addWeeks($minggu)->startOfDay();
        $kembali = $berangkat->copy()->addDays($paket->durasi_hari - 1);

        $jadwal = JadwalKeberangkatan::create([
            'id_paket' => $paket->id,
            'tanggal_berangkat' => $berangkat,
            'tanggal_kembali' => $kembali,
            'kuota_peserta' => 20,
            'sisa_kuota' => 20,
            'kota_kumpul' => $t['kota_kumpul'],
            'titik_kumpul' => $t['titik_kumpul'],
            'jam_kumpul' => $t['jam_kumpul'],
            'catatan_keberangkatan' => 'Peserta disarankan hadir di titik kumpul 15 menit sebelum jam keberangkatan.',
        ]);

        // Buat titik jemput
        foreach ($t['titik_jemput'] as $idx => $tj) {
            TitikJemput::create([
                'id_jadwal' => $jadwal->id,
                'nama' => $tj['nama'],
                'jam' => $tj['jam'],
                'urutan' => $idx + 1,
                'biaya_tambahan' => $tj['biaya_tambahan'],
            ]);
        }

        // Tugaskan kru (guide & driver)
        $petugasIds = Petugas::pluck('id')->all();
        if (!empty($petugasIds)) {
            Penugasan::create([
                'id_jadwal' => $jadwal->id,
                'id_petugas' => $petugasIds[0], // Guide
                'tanggal_tugas' => $berangkat,
            ]);
            Penugasan::create([
                'id_jadwal' => $jadwal->id,
                'id_petugas' => $petugasIds[count($petugasIds) - 1], // Driver
                'tanggal_tugas' => $berangkat,
            ]);
        }
    }

    echo "Sukses membuat paket: {$paket->nama_paket} (Min: {$paket->min_peserta} pax) beserta 3 jadwal!" . PHP_EOL;
}
