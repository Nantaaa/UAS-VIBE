<?php

use App\Models\Destinasi;
use App\Models\PaketWisata;
use App\Models\JadwalKeberangkatan;
use App\Models\TitikJemput;
use App\Models\Penugasan;
use App\Models\Petugas;

// 1. Sinkronisasi foto destinasi & paket yang sudah ada agar tidak ngawur
$destPhotoMap = [
    'Labuan Bajo' => 'destinasi/komodo.jpg',
    'Gunung Bromo' => 'destinasi/bromo.jpg',
    'Raja Ampat' => 'destinasi/raja_ampat.jpg',
    'Nusa Penida' => 'destinasi/nusa_penida.jpg',
    'Kepulauan Derawan' => 'destinasi/derawan.jpg',
    'Pulau Sumba' => 'destinasi/sumba.jpg',
    'Yogyakarta' => 'destinasi/yogyakarta.jpg',
    'Lombok' => 'destinasi/lombok.jpg',
    'Danau Toba' => 'destinasi/danau_toba.jpg',
    'Wakatobi' => 'destinasi/wakatobi.jpg',
    'Belitung' => 'destinasi/belitung.jpg',
    'Dieng' => 'destinasi/dieng.jpg',
    'Karimunjawa' => 'destinasi/karimunjawa.jpg',
    'Bunaken' => 'destinasi/bunaken.jpg',
    'Bali' => 'destinasi/bali_resort.jpg',
];

foreach ($destPhotoMap as $name => $foto) {
    Destinasi::where('nama_destinasi', 'like', "%{$name}%")->update(['foto' => $foto]);
}

$paketPhotoMap = [
    'Komodo' => 'paket/komodo.jpg',
    'Bromo & Madakaripura' => 'paket/bromo.jpg',
    'Raja Ampat' => 'paket/raja_ampat.jpg',
    'Nusa Penida' => 'paket/nusa_penida.jpg',
    'Derawan' => 'paket/derawan.jpg',
    'Sumba' => 'paket/sumba.jpg',
    'Jogja' => 'paket/yogyakarta.jpg',
    'Lombok' => 'paket/lombok.jpg',
    'Danau Toba' => 'paket/danau_toba.jpg',
    'Wakatobi' => 'paket/wakatobi.jpg',
    'Belitung' => 'paket/belitung.jpg',
    'Dieng' => 'paket/dieng.jpg',
    'Karimunjawa' => 'paket/karimunjawa.jpg',
    'Bunaken' => 'paket/bunaken.jpg',
    'Bali Romantic' => 'paket/bali_resort.jpg',
];

foreach ($paketPhotoMap as $kw => $foto) {
    PaketWisata::where('nama_paket', 'like', "%{$kw}%")->update(['foto' => $foto]);
}

// 2. Tambah 7 Paket & Destinasi Baru Unggulan Nusantara
$catalogBaru = [
    [
        'destinasi' => [
            'nama_destinasi' => 'Kawah Ijen & Baluran',
            'lokasi' => 'Banyuwangi, Jawa Timur',
            'deskripsi' => 'Fenomena api biru (Blue Fire) abadi kawah belerang terbesar di dunia dan savana liar Taman Nasional Baluran Africa van Java.',
            'foto' => 'destinasi/kawah_ijen.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Midnight Blue Fire Ijen & Eksotika Baluran 3H2M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 3,
            'tarif' => 975000,
            'harga_asli' => 1250000,
            'is_bestseller' => true,
            'foto' => 'paket/kawah_ijen.jpg',
            'highlight' => 'Open Trip • Blue Fire • Savana Baluran',
            'deskripsi' => 'Menyaksikan fenomena Blue Fire langka di Kawah Ijen, sunrise danau toska asam, hutan purba De Djawatan Benculuk, dan savana Bekol Baluran.',
            'fasilitas' => "Hotel AC Banyuwangi (2 malam)\nTrooper Jeep 4WD Ijen\nMasker gas respirator & kacamata goggle\nTiket masuk Baluran & Ijen\nGuide & porter belerang lokal\nMakan 5x khas Banyuwangi",
            'termasuk' => "Transportasi wisata AC selama trip\nJeep 4WD Paltuding Ijen\nMasker gas respirator standar\nTiket masuk Ijen, Baluran, Djawatan\nPemandu wisata resmi Banyuwangi",
            'tidak_termasuk' => "Tiket kereta / pesawat ke Banyuwangi\nSewa troli manusia (troli gerobak Ijen)\nPengeluaran pribadi",
            'itinerary' => "Hari 1: Penjemputan di Stasiun Banyuwangi Kota / Ketapang - Eksplorasi hutan lord of the rings De Djawatan - Sunset Pantai Marina Boom\nHari 2: Dini hari pukul 00:30 start pendakian Ijen - Menyaksikan Blue Fire & Sunrise Kawah Toska - Kembali ke hotel istirahat - Siang menuju TN Baluran Savana Bekol & Pantai Bama\nHari 3: Berburu kuliner Nasi Tempong & Pia Glenmore - Belanja oleh-oleh khas Osing - Pengantaran ke Stasiun",
        ],
        'kota_kumpul' => 'Banyuwangi',
        'titik_kumpul' => 'Stasiun Banyuwangi Kota / Pelabuhan Ketapang',
        'jam_kumpul' => '08:30',
        'titik_jemput' => [
            ['nama' => 'Stasiun Banyuwangi Kota — pintu keluar', 'jam' => '08:30', 'biaya_tambahan' => 0],
            ['nama' => 'Pelabuhan Ketapang Banyuwangi', 'jam' => '09:15', 'biaya_tambahan' => 0],
            ['nama' => 'Bandara Banyuwangi (BWX)', 'jam' => '09:45', 'biaya_tambahan' => 35000],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Banda Neira',
            'lokasi' => 'Kepulauan Banda, Maluku Tengah',
            'deskripsi' => 'Nol kilometer jalur rempah dunia, benteng peninggalan VOC, dan pesona bawah laut Lava Flow di bawah kaki Gunung Api Banda.',
            'foto' => 'destinasi/banda_neira.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Jejak Rempah Nusantara & Lava Flow Banda Neira 5H4M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 5,
            'tarif' => 4750000,
            'harga_asli' => 5500000,
            'is_bestseller' => true,
            'foto' => 'paket/banda_neira.jpg',
            'highlight' => 'Open Trip • Sejarah VOC • Lava Flow',
            'deskripsi' => 'Menjelajahi Benteng Belgica, rumah pengasingan Bung Hatta, perkebunan pala tertua, serta snorkeling magis di terumbu karang Lava Flow dan Pulau Hatta.',
            'fasilitas' => "Penginapan heritage Banda Neira (4 malam)\nTiket kapal cepat PP Ambon - Banda\nSewa perahu tradisional island hopping\nAlat snorkeling & life jacket\nMakan 12x kuliner rempah Maluku",
            'termasuk' => "Akomodasi kamar ber-AC di Pulau Neira\nTiket kapal cepat PP Ambon-Banda\nKapal keliling Pulau Hatta, Rhun, & Gunung Api\nTiket situs bersejarah & retribusi\nGuide lokal sejarawan Banda",
            'tidak_termasuk' => "Tiket pesawat ke Kota Ambon\nPengeluaran pribadi & suvenir manisan pala\nSewa alat diving (opsional)",
            'itinerary' => "Hari 1: Berangkat dari Pelabuhan Tulehu Ambon via kapal cepat ke Banda Neira - Check-in hotel - Sunset di Benteng Belgica\nHari 2: City tour sejarah Banda: Rumah Hatta, Sjahrir, Gereja Tua, Istana Mini VOC - Snorkeling sore di Lava Flow kaki Gunung Api\nHari 3: Island hopping ke Pulau Hatta - Snorkeling pantai berpasir halus & drop-off karang spektakuler - Kunjungan Pulau Syahrir\nHari 4: Mengunjungi perkebunan pohon pala tertua di Pulau Banda Besar - Benteng Hollandia - Kuliner sup ikan kuah asam & selai pala\nHari 5: Belanja oleh-oleh kayu manis & pala - Penyeberangan kapal cepat kembali ke Ambon",
        ],
        'kota_kumpul' => 'Ambon',
        'titik_kumpul' => 'Pelabuhan Tulehu Ambon / Bandara Pattimura',
        'jam_kumpul' => '07:00',
        'titik_jemput' => [
            ['nama' => 'Pelabuhan Tulehu Ambon — dermaga kapal cepat', 'jam' => '07:00', 'biaya_tambahan' => 0],
            ['nama' => 'Bandara Pattimura Ambon (AMQ)', 'jam' => '05:30', 'biaya_tambahan' => 85000],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Tana Toraja',
            'lokasi' => 'Makale & Rantepao, Sulawesi Selatan',
            'deskripsi' => 'Negeri magis tongkonan, ritual adat Rambu Solo, kuburan batu gantung di tebing cadas Londa, dan patung kayu Tau-Tau.',
            'foto' => 'destinasi/toraja.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Magis Budaya Leluhur & Negeri di Atas Awan Toraja 4H3M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 4,
            'tarif' => 2450000,
            'harga_asli' => 2900000,
            'is_bestseller' => false,
            'foto' => 'paket/toraja.jpg',
            'highlight' => 'Open Trip • Desa Kete Kesu • Londa',
            'deskripsi' => 'Menyelami keagungan arsitektur rumah adat Tongkonan di Kete Kesu, makam batu Lemo, gua makam Londa, dan panorama samudera awan Lolai.',
            'fasilitas' => "Hotel resort bintang 3 di Rantepao (3 malam)\nTransportasi bus pariwisata eksekutif Makassar-Toraja PP\nTiket masuk seluruh objek adat\nPemandu adat lokal Toraja\nMakan 8x masakan khas Toraja",
            'termasuk' => "Akomodasi 3 malam di Toraja\nTransportasi bus pariwisata AC nyaman\nTiket masuk situs Kete Kesu, Lemo, Londa, Bori Parinding\nPemandu budaya lokal bersertifikat",
            'tidak_termasuk' => "Tiket pesawat ke Makassar (UPG)\nSumbangan rokok/sirih adat pada upacara adat\nPengeluaran pribadi",
            'itinerary' => "Hari 1: Meeting point Makassar - Perjalanan panorama melintasi pesisir Parepare & Gunung Nona Enrekang - Tiba di Rantepao\nHari 2: Eksplorasi Desa Adat Kete Kesu - Makam tebing batu Lemo & patung Tau-Tau - Menjelajahi gua cadas Londa dengan lentera damar\nHari 3: Subuh menikmati Samudera Awan di Negeri di Atas Awan Puncak Lolai - Kompleks megalitikum Bori Parinding - Kebun Kopi Arabika Toraja\nHari 4: Belanja kain tenun & kopi Toraja - Perjalanan kembali ke Makassar",
        ],
        'kota_kumpul' => 'Makassar',
        'titik_kumpul' => 'Bandara Sultan Hasanuddin (UPG) Makassar',
        'jam_kumpul' => '08:00',
        'titik_jemput' => [
            ['nama' => 'Bandara Internasional Sultan Hasanuddin (UPG)', 'jam' => '08:00', 'biaya_tambahan' => 0],
            ['nama' => 'Hotel area Pantai Losari Makassar', 'jam' => '08:45', 'biaya_tambahan' => 0],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Kepulauan Seribu',
            'lokasi' => 'Kepulauan Seribu, DKI Jakarta',
            'deskripsi' => 'Pelarian singkat bahari di utara Jakarta dengan pulau tak berpenghuni, terumbu karang jernih, dan matahari tenggelam eksotis.',
            'foto' => 'destinasi/pulau_seribu.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Island Hopping Bahari & Snorkeling Pulau Harapan 2H1M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 2,
            'tarif' => 450000,
            'harga_asli' => 580000,
            'is_bestseller' => true,
            'foto' => 'paket/pulau_seribu.jpg',
            'highlight' => 'Open Trip • Island Hopping • Dekat Jakarta',
            'deskripsi' => 'Trip hemat akhir pekan menjelajah Pulau Perak, Pulau Dolphin pasir timbul, snorkeling karang Bira, dan penangkaran penyu sisik.',
            'fasilitas' => "Tiket kapal penyeberangan Marina Ancol / Muara Angke PP\nHomestay AC di Pulau Harapan (1 malam)\nKapal tradisional sewa jelajah pulau 2 hari\nAlat snorkeling & pelampung lengkap\nMakan 3x + BBQ seafood malam",
            'termasuk' => "Transportasi kapal PP\nHomestay AC nyaman\nKapal jelajah pulau & spot snorkeling\nTiket retribusi pulau & penangkaran penyu\nTour leader & instruktur snorkeling",
            'tidak_termasuk' => "Transportasi ke pelabuhan meeting point\nPengeluaran pribadi di pulau\nTiket masuk gerbang Ancol (jika via Marina)",
            'itinerary' => "Hari 1: Berkumpul di dermaga - Menyeberang ke Pulau Harapan - Check-in homestay - Jelajah Pulau Perak, Pulau Gosong Bintang, snorkeling Karang Genteng - Sunset Pulau Bulat - BBQ seafood\nHari 2: Sunrise di dermaga pulau - Snorkeling pagi di Pulau Macan Gundul - Kunjungan penangkaran penyu sisik - Penyeberangan kembali ke Jakarta",
        ],
        'kota_kumpul' => 'Jakarta',
        'titik_kumpul' => 'Dermaga Kaliadem Muara Angke / Dermaga 16 Marina Ancol',
        'jam_kumpul' => '06:30',
        'titik_jemput' => [
            ['nama' => 'Dermaga Kaliadem Muara Angke — loket tiket', 'jam' => '06:30', 'biaya_tambahan' => 0],
            ['nama' => 'Dermaga Marina Ancol Gate 16 (Speedboat)', 'jam' => '07:15', 'biaya_tambahan' => 150000],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Padang & Bukittinggi',
            'lokasi' => 'Sumatera Barat',
            'deskripsi' => 'Keajaiban alam lembah tebing cadas Harau, keelokan Danau Maninjau 44 kelok, Jam Gadang, dan kuliner rendang Minang otentik.',
            'foto' => 'destinasi/padang.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Pesona Ranah Minang & Tebing Harau Valley 4H3M',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 4,
            'tarif' => 1890000,
            'harga_asli' => 2250000,
            'is_bestseller' => false,
            'foto' => 'paket/padang.jpg',
            'highlight' => 'Open Trip • Lembah Harau • Jam Gadang',
            'deskripsi' => 'Menikmati megahnya tebing granit raksasa Lembah Harau mirip Yosemite, Ngarai Sianok, Lobang Jepang, Istano Basa Pagaruyung, dan kuliner legendaris.',
            'fasilitas' => "Hotel bintang 3 di Bukittinggi & Padang (3 malam)\nTransportasi armada wisata AC full trip\nTiket masuk seluruh destinasi\nGuide lokal ramah berbahasa Minang & Indonesia\nMakan 8x rendang, gulai tunjang, sate padang",
            'termasuk' => "Akomodasi hotel 3 malam\nTransportasi darat ber-AC selama tour\nTiket masuk objek wisata Lembah Harau, Istano Pagaruyung, Ngarai Sianok\nPemandu wisata berlisensi",
            'tidak_termasuk' => "Tiket pesawat ke Padang (PDG)\nBelanja oleh-oleh Keripik Balado Sanjai\nPengeluaran pribadi",
            'itinerary' => "Hari 1: Penjemputan di Bandara Minangkabau (BIM) - Air Terjun Lembah Anai - Pusat Kerajinan Pandai Sikek - Check-in hotel Bukittinggi - Jam Gadang\nHari 2: Panorama Ngarai Sianok & Lobang Jepang - Perjalanan ke Tebing Granit Lembah Harau & Sarasah Bunta - Istano Basa Pagaruyung\nHari 3: Desa terindah Pariangan - Panorama Danau Singkarak - Meluncur ke Kota Padang - Sunset di Pantai Padang & Jembatan Siti Nurbaya\nHari 4: Belanja oleh-oleh keripik balado & rendang - Masjid Raya Sumatera Barat - Transfer Bandara BIM",
        ],
        'kota_kumpul' => 'Padang',
        'titik_kumpul' => 'Bandara Internasional Minangkabau (BIM) Padang',
        'jam_kumpul' => '09:00',
        'titik_jemput' => [
            ['nama' => 'Bandara Internasional Minangkabau (BIM) — lobi kedatangan', 'jam' => '09:00', 'biaya_tambahan' => 0],
            ['nama' => 'Stasiun Padang / Hotel area Padang Kota', 'jam' => '09:45', 'biaya_tambahan' => 0],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Gunung Bromo',
            'lokasi' => 'Probolinggo & Pasuruan, Jawa Timur',
            'deskripsi' => 'Lautan pasir berbisik dan kawah aktif spektakuler di kawasan Taman Nasional Bromo Tengger Semeru.',
            'foto' => 'destinasi/bromo.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Bromo Midnight Express Sunrise & Pasir Berbisik 1H Tanpa Inap',
            'tipe_trip' => 'Open Trip',
            'min_peserta' => 1,
            'durasi_hari' => 1,
            'tarif' => 375000,
            'harga_asli' => 480000,
            'is_bestseller' => true,
            'foto' => 'paket/bromo.jpg',
            'highlight' => 'Open Trip • Hemat 1 Hari • Jeep 4WD',
            'deskripsi' => 'Paket kilat paling efisien berangkat tengah malam dari Malang atau Surabaya: golden sunrise Penanjakan, kawah Bromo, Pasir Berbisik, dan Bukit Teletubbies.',
            'fasilitas' => "Jeep 4WD Hardtop full spot\nTransportasi penjemputan AC dari Malang/Surabaya\nTiket masuk TN Bromo Tengger Semeru\nDriver merangkap pemandu & fotografer\nSnack & air mineral",
            'termasuk' => "Transport PP meeting point - Bromo\nJeep Hardtop 4x4 kapasitas 6 orang\nTiket masuk Bromo resmi\nSpot: Penanjakan 1, Kawah Bromo, Pura Poten, Pasir Berbisik, Savana Teletubbies",
            'tidak_termasuk' => "Sewa kuda di lautan pasir Bromo (opsional)\nPengeluaran pribadi & makan di luar paket\nSewa jaket hangat",
            'itinerary' => "Pukul 23:30 (Malam): Penjemputan di Malang/Surabaya - Perjalanan menuju transit jeep\nPukul 03:00: Naik Jeep 4WD menuju titik Penanjakan 1 - Menanti Sunrise spektakuler Bromo\nPukul 06:00: Turun ke lautan pasir - Mendaki bibir Kawah Bromo & Pura Luhur Poten\nPukul 08:30: Foto di Pasir Berbisik & Bukit Savana Teletubbies\nPukul 10:30: Kembali ke transit point - Perjalanan pulang ke Malang/Surabaya",
        ],
        'kota_kumpul' => 'Malang',
        'titik_kumpul' => 'Stasiun Malang Kotabaru / Stasiun Pasar Turi Surabaya',
        'jam_kumpul' => '23:30',
        'titik_jemput' => [
            ['nama' => 'Stasiun Malang Kotabaru — pintu barat', 'jam' => '23:30', 'biaya_tambahan' => 0],
            ['nama' => 'Stasiun Surabaya Gubeng / Pasar Turi', 'jam' => '22:00', 'biaya_tambahan' => 60000],
        ]
    ],
    [
        'destinasi' => [
            'nama_destinasi' => 'Bali',
            'lokasi' => 'Seminyak & Ubud, Bali',
            'deskripsi' => 'Resor tepi tebing, pura laut mistis, dan keindahan persawahan terasering Ubud.',
            'foto' => 'destinasi/bali_resort.jpg',
        ],
        'paket' => [
            'nama_paket' => 'Bali Private Honeymoon Pool Villa & Sunset Dinner 4H3M',
            'tipe_trip' => 'Honeymoon',
            'min_peserta' => 2,
            'durasi_hari' => 4,
            'tarif' => 4200000,
            'harga_asli' => 5000000,
            'is_bestseller' => false,
            'foto' => 'paket/bali_resort.jpg',
            'highlight' => 'Honeymoon • Private Pool • Romantic Dinner',
            'deskripsi' => 'Paket bulan madu romantis 4 hari 3 malam dengan menginap di Luxury One Bedroom Private Pool Villa, dinner candlelight di Jimbaran beach, dan spa pasangan eksklusif.',
            'fasilitas' => "Luxury One Bedroom Private Pool Villa 3 malam\nMobil privat Toyota Innova Reborn + driver pribadi\nRomantic Candlelight Dinner di Jimbaran Seafood\nCouple Balinese Massage & Spa Treatment 2 Jam\nDekorasi bunga romantis di bathtub & ranjang villa",
            'termasuk' => "Akomodasi Luxury Villa private pool\nSemua transportasi privat eksklusif\nCandlelight dinner romantis di tepi pantai\nTiket wisata Tanah Lot, Uluwatu, Tegalalang\nWelcome drink & floating breakfast di private pool",
            'tidak_termasuk' => "Tiket pesawat ke Bali (DPS)\nPengeluaran belanja pribadi",
            'itinerary' => "Hari 1: Penjemputan eksklusif di Bandara Ngurah Rai - Check-in Private Pool Villa Seminyak - Romantic sunset dinner Jimbaran beach\nHari 2: Wisata romantis Ubud: Swing tegalalang, Monkey Forest, Tirta Empul - Spa pasangan 2 jam di resor bintang 5\nHari 3: Melasti Beach club - Sunset Pura Uluwatu menyaksikan tari Kecak spektakuler - Dinner mewah di Samudera Restaurant\nHari 4: Floating breakfast di kolam renang villa - Belanja oleh-oleh khas Bali Krisna - Pengantaran VIP ke Bandara",
        ],
        'kota_kumpul' => 'Denpasar',
        'titik_kumpul' => 'Bandara Internasional I Gusti Ngurah Rai (DPS) Denpasar',
        'jam_kumpul' => '11:00',
        'titik_jemput' => [
            ['nama' => 'Bandara Ngurah Rai (DPS) — Pick up zone kedatangan', 'jam' => '11:00', 'biaya_tambahan' => 0],
            ['nama' => 'Hotel area Kuta / Seminyak / Legian', 'jam' => '11:45', 'biaya_tambahan' => 0],
        ]
    ],
];

foreach ($catalogBaru as $item) {
    // 1. Destinasi
    $dData = $item['destinasi'];
    $dest = Destinasi::firstOrCreate(
        ['nama_destinasi' => $dData['nama_destinasi']],
        [
            'lokasi' => $dData['lokasi'],
            'deskripsi' => $dData['deskripsi'],
            'foto' => $dData['foto'],
        ]
    );

    // 2. Paket
    $pData = $item['paket'];
    $pData['id_destinasi'] = $dest->id;
    $pData['is_active'] = true;

    $paket = PaketWisata::create($pData);

    // 3. Buat 3 jadwal keberangkatan untuk masing-masing paket baru
    foreach ([2, 4, 7] as $minggu) {
        $berangkat = now()->addWeeks($minggu)->startOfDay();
        $kembali = $berangkat->copy()->addDays($paket->durasi_hari - 1);

        $jadwal = JadwalKeberangkatan::create([
            'id_paket' => $paket->id,
            'tanggal_berangkat' => $berangkat,
            'tanggal_kembali' => $kembali,
            'kuota_peserta' => 20,
            'sisa_kuota' => 20,
            'kota_kumpul' => $item['kota_kumpul'],
            'titik_kumpul' => $item['titik_kumpul'],
            'jam_kumpul' => $item['jam_kumpul'],
            'catatan_keberangkatan' => 'Peserta disarankan hadir di titik kumpul 15 menit sebelum waktu keberangkatan.',
        ]);

        // Titik jemput
        foreach ($item['titik_jemput'] as $idx => $tj) {
            TitikJemput::create([
                'id_jadwal' => $jadwal->id,
                'nama' => $tj['nama'],
                'jam' => $tj['jam'],
                'urutan' => $idx + 1,
                'biaya_tambahan' => $tj['biaya_tambahan'],
            ]);
        }

        // Penugasan petugas kru
        $petugasIds = Petugas::pluck('id')->all();
        if (!empty($petugasIds)) {
            Penugasan::create([
                'id_jadwal' => $jadwal->id,
                'id_petugas' => $petugasIds[0],
                'tanggal_tugas' => $berangkat,
            ]);
            Penugasan::create([
                'id_jadwal' => $jadwal->id,
                'id_petugas' => $petugasIds[count($petugasIds) - 1],
                'tanggal_tugas' => $berangkat,
            ]);
        }
    }

    echo "Sukses membuat: {$paket->nama_paket} (Min: {$paket->min_peserta} pax)!" . PHP_EOL;
}

echo "Semua paket berhasil ditambahkan & foto disinkronkan!" . PHP_EOL;
