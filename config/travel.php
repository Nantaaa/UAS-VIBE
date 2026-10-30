<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif Anak
    |--------------------------------------------------------------------------
    | Persentase tarif peserta kategori anak terhadap tarif dewasa.
    | 50 berarti anak membayar 50% dari harga per orang.
    */
    'tarif_anak_persen' => (int) env('TARIF_ANAK_PERSEN', 50),

    // Batas jumlah peserta dalam satu pemesanan
    'maks_peserta' => 20,
    'maks_anak' => 10,
];
