<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use App\Models\PaketWisata;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaketController extends Controller
{
    public function index(Request $request)
    {
        $paket = PaketWisata::with('destinasi')
            ->withCount('jadwal')
            ->when($request->filled('q'), fn ($q) => $q->where('nama_paket', 'like', '%'.$request->input('q').'%'))
            ->orderBy('nama_paket')
            ->paginate(10)
            ->withQueryString();

        return view('admin.paket.index', compact('paket'));
    }

    public function create()
    {
        return view('admin.paket.form', [
            'paket' => new PaketWisata(['durasi_hari' => 1, 'is_active' => true]),
            'jadwalPertama' => null,
            'destinasi' => Destinasi::orderBy('nama_destinasi')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->input('id_destinasi') === 'baru') {
            $destData = [
                'nama_destinasi' => $request->input('destinasi_baru_nama'),
                'lokasi' => $request->input('destinasi_baru_lokasi'),
                'deskripsi' => $request->input('destinasi_baru_deskripsi') ?: 'Destinasi wisata unggulan ' . $request->input('destinasi_baru_nama') . '.',
            ];
            if ($request->hasFile('destinasi_baru_foto')) {
                $destData['foto'] = $request->file('destinasi_baru_foto')->store('destinasi', 'public');
            } elseif ($request->hasFile('foto')) {
                $destData['foto'] = $request->file('foto')->store('destinasi', 'public');
            }
            $destinasiBaru = Destinasi::create($destData);
            $data['id_destinasi'] = $destinasiBaru->id;
        }

        unset($data['destinasi_baru_nama'], $data['destinasi_baru_lokasi'], $data['destinasi_baru_deskripsi'], $data['destinasi_baru_foto']);

        // Data inisialisasi jadwal
        $tglBerangkatStr = $request->input('tanggal_berangkat');
        $kuotaPeserta = (int) $request->input('kuota_peserta', 20);
        $kotaKumpul = $request->input('kota_kumpul');
        $jamKumpul = $request->input('jam_kumpul', '08:00');
        $titikKumpul = $request->input('titik_kumpul');
        $catatanKumpul = $request->input('catatan_keberangkatan');

        unset($data['tanggal_berangkat'], $data['kuota_peserta'], $data['kota_kumpul'], $data['jam_kumpul'], $data['titik_kumpul'], $data['catatan_keberangkatan']);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        $data['include_penerbangan'] = $request->boolean('include_penerbangan');
        $data['harga_asli'] = $request->filled('harga_asli') ? $request->input('harga_asli') : null;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('paket', 'public');
        }

        $paketBaru = PaketWisata::create($data);

        // Buat jadwal keberangkatan perdana otomatis agar paket langsung TERSEDIA dan memiliki kuota & titik kumpul
        if ($tglBerangkatStr) {
            $tglBerangkat = \Carbon\Carbon::parse($tglBerangkatStr);
            $durasi = max(1, (int) $paketBaru->durasi_hari);
            $tglKembali = (clone $tglBerangkat)->addDays(max(0, $durasi - 1));

            \App\Models\JadwalKeberangkatan::create([
                'id_paket' => $paketBaru->id,
                'tanggal_berangkat' => $tglBerangkat,
                'tanggal_kembali' => $tglKembali,
                'kuota_peserta' => $kuotaPeserta > 0 ? $kuotaPeserta : 20,
                'sisa_kuota' => $kuotaPeserta > 0 ? $kuotaPeserta : 20,
                'kota_kumpul' => $kotaKumpul ?: ($paketBaru->destinasi->nama_destinasi ?? 'Area Destinasi'),
                'titik_kumpul' => $titikKumpul ?: 'Bandara / Titik Kumpul Utama ' . ($paketBaru->destinasi->nama_destinasi ?? ''),
                'jam_kumpul' => $jamKumpul,
                'catatan_keberangkatan' => $catatanKumpul,
            ]);
        }

        $pesan = isset($destinasiBaru)
            ? 'Paket wisata, kuota kursi (' . $kuotaPeserta . ' pax), dan destinasi baru "' . $destinasiBaru->nama_destinasi . '" berhasil ditambahkan.'
            : 'Paket wisata dan kuota kursi (' . $kuotaPeserta . ' pax) berhasil ditambahkan.';

        return redirect()->route('admin.paket.index')->with('sukses', $pesan);
    }

    public function edit(PaketWisata $paket)
    {
        return view('admin.paket.form', [
            'paket' => $paket,
            'jadwalPertama' => $paket->jadwalTersedia->first() ?? $paket->jadwal()->orderByDesc('tanggal_berangkat')->first(),
            'destinasi' => Destinasi::orderBy('nama_destinasi')->get(),
        ]);
    }

    public function update(Request $request, PaketWisata $paket)
    {
        $data = $this->validasi($request);

        if ($request->input('id_destinasi') === 'baru') {
            $destData = [
                'nama_destinasi' => $request->input('destinasi_baru_nama'),
                'lokasi' => $request->input('destinasi_baru_lokasi'),
                'deskripsi' => $request->input('destinasi_baru_deskripsi') ?: 'Destinasi wisata unggulan ' . $request->input('destinasi_baru_nama') . '.',
            ];
            if ($request->hasFile('destinasi_baru_foto')) {
                $destData['foto'] = $request->file('destinasi_baru_foto')->store('destinasi', 'public');
            } elseif ($request->hasFile('foto')) {
                $destData['foto'] = $request->file('foto')->store('destinasi', 'public');
            }
            $destinasiBaru = Destinasi::create($destData);
            $data['id_destinasi'] = $destinasiBaru->id;
        }

        unset($data['destinasi_baru_nama'], $data['destinasi_baru_lokasi'], $data['destinasi_baru_deskripsi'], $data['destinasi_baru_foto']);

        // Data inisialisasi jadwal
        $tglBerangkatStr = $request->input('tanggal_berangkat');
        $kuotaPeserta = (int) $request->input('kuota_peserta', 20);
        $kotaKumpul = $request->input('kota_kumpul');
        $jamKumpul = $request->input('jam_kumpul', '08:00');
        $titikKumpul = $request->input('titik_kumpul');
        $catatanKumpul = $request->input('catatan_keberangkatan');

        unset($data['tanggal_berangkat'], $data['kuota_peserta'], $data['kota_kumpul'], $data['jam_kumpul'], $data['titik_kumpul'], $data['catatan_keberangkatan']);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        $data['include_penerbangan'] = $request->boolean('include_penerbangan');
        $data['harga_asli'] = $request->filled('harga_asli') ? $request->input('harga_asli') : null;

        if ($request->hasFile('foto')) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }
            $data['foto'] = $request->file('foto')->store('paket', 'public');
        }

        $paket->update($data);

        // Update atau buat jadwal perdana jika diisi
        if ($tglBerangkatStr) {
            $jadwalAktif = $paket->jadwalTersedia->first() ?? $paket->jadwal()->orderByDesc('tanggal_berangkat')->first();
            $tglBerangkat = \Carbon\Carbon::parse($tglBerangkatStr);
            $durasi = max(1, (int) $paket->durasi_hari);
            $tglKembali = (clone $tglBerangkat)->addDays(max(0, $durasi - 1));

            if ($jadwalAktif) {
                $terpakai = $jadwalAktif->kuotaTerpakai();
                $kuotaBaru = max($kuotaPeserta, $terpakai);
                $jadwalAktif->update([
                    'tanggal_berangkat' => $tglBerangkat,
                    'tanggal_kembali' => $tglKembali,
                    'kuota_peserta' => $kuotaBaru,
                    'sisa_kuota' => max(0, $kuotaBaru - $terpakai),
                    'kota_kumpul' => $kotaKumpul ?: $jadwalAktif->kota_kumpul,
                    'titik_kumpul' => $titikKumpul ?: $jadwalAktif->titik_kumpul,
                    'jam_kumpul' => $jamKumpul ?: $jadwalAktif->jam_kumpul,
                    'catatan_keberangkatan' => $catatanKumpul,
                ]);
            } else {
                \App\Models\JadwalKeberangkatan::create([
                    'id_paket' => $paket->id,
                    'tanggal_berangkat' => $tglBerangkat,
                    'tanggal_kembali' => $tglKembali,
                    'kuota_peserta' => $kuotaPeserta > 0 ? $kuotaPeserta : 20,
                    'sisa_kuota' => $kuotaPeserta > 0 ? $kuotaPeserta : 20,
                    'kota_kumpul' => $kotaKumpul ?: ($paket->destinasi->nama_destinasi ?? 'Area Destinasi'),
                    'titik_kumpul' => $titikKumpul ?: 'Bandara / Titik Kumpul Utama ' . ($paket->destinasi->nama_destinasi ?? ''),
                    'jam_kumpul' => $jamKumpul,
                    'catatan_keberangkatan' => $catatanKumpul,
                ]);
            }
        }

        return redirect()->route('admin.paket.index')->with('sukses', 'Paket wisata berhasil diperbarui.');
    }

    public function destroy(PaketWisata $paket)
    {
        $adaPesananTerkonfirmasi = $paket->jadwal()
            ->whereHas('pemesanan', fn ($q) => $q->whereIn('status_pemesanan', ['terkonfirmasi']))
            ->exists();

        if ($adaPesananTerkonfirmasi) {
            return back()->withErrors([
                'paket' => 'Paket wisata tidak dapat dihapus permanen karena sudah memiliki riwayat pemesanan terkonfirmasi. Silakan nonaktifkan saja statusnya agar tidak tampil di katalog.',
            ]);
        }

        $namaPaket = $paket->nama_paket;

        DB::transaction(function () use ($paket) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }

            foreach ($paket->jadwal as $j) {
                foreach ($j->pemesanan as $p) {
                    $p->peserta()->delete();
                    $p->pembayaran()->delete();
                    $p->delete();
                }
                $j->penugasan()->delete();
                $j->titikJemput()->delete();
                $j->delete();
            }

            Wishlist::where('id_paket', $paket->id)->delete();
            $paket->delete();
        });

        return redirect()->route('admin.paket.index')->with('sukses', "Paket wisata \"{$namaPaket}\" berhasil dihapus permanen dari sistem.");
    }

    public function exportExcel(): StreamedResponse
    {
        $daftarPaket = PaketWisata::with('destinasi')->withCount('jadwal')->orderBy('nama_paket')->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Nanta Travel System')
            ->setTitle('Katalog Master Paket Wisata Nanta Travel');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Katalog Paket Wisata');
        $sheet->setShowGridLines(true);

        // Header Title
        $sheet->setCellValue('A1', 'PT NANTA EKSPEDISI NUSANTARA — NANTA TRAVEL');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(14)->setBold(true)->getColor()->setARGB('FF0F2C4A');

        $sheet->setCellValue('A2', 'KATALOG MASTER DATA PAKET WISATA & TARIF RESMI');
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(11)->setBold(true)->getColor()->setARGB('FF334155');

        $sheet->setCellValue('A3', 'Total Paket: ' . $daftarPaket->count() . ' paket | Dicetak: ' . now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(9)->setItalic(true)->getColor()->setARGB('FF64748B');

        $headers = [
            'A5' => ['No', 6, Alignment::HORIZONTAL_CENTER],
            'B5' => ['ID', 8, Alignment::HORIZONTAL_CENTER],
            'C5' => ['Nama Paket Wisata', 34, Alignment::HORIZONTAL_LEFT],
            'D5' => ['Destinasi', 20, Alignment::HORIZONTAL_LEFT],
            'E5' => ['Wilayah / Lokasi', 24, Alignment::HORIZONTAL_LEFT],
            'F5' => ['Tipe Trip', 14, Alignment::HORIZONTAL_CENTER],
            'G5' => ['Durasi', 12, Alignment::HORIZONTAL_CENTER],
            'H5' => ['Min Pax', 10, Alignment::HORIZONTAL_RIGHT],
            'I5' => ['Tarif Open Trip (Rp)', 18, Alignment::HORIZONTAL_RIGHT],
            'J5' => ['Tarif Private (Rp)', 18, Alignment::HORIZONTAL_RIGHT],
            'K5' => ['Harga Asli (Rp)', 18, Alignment::HORIZONTAL_RIGHT],
            'L5' => ['Termasuk Pesawat', 16, Alignment::HORIZONTAL_CENTER],
            'M5' => ['Best Seller', 12, Alignment::HORIZONTAL_CENTER],
            'N5' => ['Status Katalog', 14, Alignment::HORIZONTAL_CENTER],
            'O5' => ['Jadwal Aktif', 12, Alignment::HORIZONTAL_RIGHT],
        ];

        foreach ($headers as $cell => [$label, $width, $align]) {
            $sheet->setCellValue($cell, $label);
            $sheet->getColumnDimension(preg_replace('/[0-9]/', '', $cell))->setWidth($width);
        }

        $headerRange = 'A5:O5';
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension(5)->setRowHeight(25);

        $row = 6;
        foreach ($daftarPaket as $idx => $p) {
            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", "#{$p->id}");
            $sheet->setCellValue("C{$row}", $p->nama_paket);
            $sheet->setCellValue("D{$row}", $p->destinasi->nama_destinasi ?? '—');
            $sheet->setCellValue("E{$row}", $p->destinasi->lokasi ?? '—');
            $sheet->setCellValue("F{$row}", $p->tipe_trip ?: 'Open Trip');
            $sheet->setCellValue("G{$row}", "{$p->durasi_hari}H" . max(0, $p->durasi_hari - 1) . "M");
            $sheet->setCellValue("H{$row}", $p->min_peserta ?? 1);
            $sheet->setCellValue("I{$row}", (float) $p->tarif);
            $sheet->setCellValue("J{$row}", (float) ($p->tarif_private ?: $p->tarifPrivate()));
            $sheet->setCellValue("K{$row}", (float) ($p->harga_asli ?: $p->tarif));
            $sheet->setCellValue("L{$row}", $p->include_penerbangan ? 'Ya' : 'Tidak');
            $sheet->setCellValue("M{$row}", $p->is_bestseller ? 'Ya' : 'Tidak');
            $sheet->setCellValue("N{$row}", $p->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue("O{$row}", $p->jadwal_count);

            $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("L{$row}:N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("O{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("I{$row}:K{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0');

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$row}:O{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$row}:O{$row}")->getFont()->setName('Arial')->setSize(9);
            $sheet->getStyle("A{$row}:O{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($row)->setRowHeight(19);

            $row++;
        }

        $sheet->freezePane('A6');

        $filename = "Katalog-Paket-Wisata-Nanta-Travel-" . now()->format('Y-m-d') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function validasi(Request $request): array
    {
        $rules = [
            'nama_paket' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
            'termasuk' => ['nullable', 'string'],
            'tidak_termasuk' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'highlight' => ['nullable', 'string', 'max:150'],
            'tipe_trip' => ['nullable', 'string', 'max:40'],
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'min_peserta' => ['nullable', 'integer', 'min:1', 'max:20'],
            'tarif' => ['required', 'numeric', 'min:0'],
            'tarif_private' => ['nullable', 'numeric', 'min:0'],
            'harga_asli' => ['nullable', 'numeric', 'min:0'],
            'include_penerbangan' => ['nullable', 'boolean'],
            'is_bestseller' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'kuota_peserta' => ['nullable', 'integer', 'min:1', 'max:500'],
            'tanggal_berangkat' => ['nullable', 'date'],
            'kota_kumpul' => ['nullable', 'string', 'max:100'],
            'jam_kumpul' => ['nullable', 'string'],
            'titik_kumpul' => ['nullable', 'string', 'max:255'],
            'catatan_keberangkatan' => ['nullable', 'string'],
        ];

        if ($request->input('id_destinasi') === 'baru') {
            $rules['destinasi_baru_nama'] = ['required', 'string', 'max:150'];
            $rules['destinasi_baru_lokasi'] = ['required', 'string', 'max:150'];
            $rules['destinasi_baru_deskripsi'] = ['nullable', 'string'];
            $rules['destinasi_baru_foto'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'];
        } else {
            $rules['id_destinasi'] = ['required', 'exists:destinasi,id'];
        }

        return $request->validate($rules, [
            'id_destinasi.required' => 'Destinasi wajib dipilih atau buat destinasi baru.',
            'destinasi_baru_nama.required' => 'Nama destinasi baru wajib diisi.',
            'destinasi_baru_lokasi.required' => 'Lokasi / wilayah destinasi baru wajib diisi.',
            'nama_paket.required' => 'Nama paket wajib diisi.',
            'durasi_hari.required' => 'Durasi wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.numeric' => 'Tarif harus berupa angka.',
        ]);
    }
}
