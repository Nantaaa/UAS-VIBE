<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Models\PaketWisata;
use App\Models\Petugas;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalKeberangkatan::with([
            'paket.destinasi',
            'penugasan.petugas',
            'pemesanan' => fn ($q) => $q->whereIn('status_pemesanan', ['terkonfirmasi', 'menunggu_verifikasi', 'perlu_perbaikan', 'menunggu_pembayaran'])
                ->with(['peserta', 'user']),
        ])
        ->withCount(['pemesanan as peserta_terkonfirmasi' => fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi')]);

        // 1. Pencarian Kata Kunci (Nama Paket, Destinasi, Kota Kumpul, Petugas)
        if ($cari = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($cari) {
                $q->where('kota_kumpul', 'like', "%{$cari}%")
                    ->orWhere('titik_kumpul', 'like', "%{$cari}%")
                    ->orWhereDate('tanggal_berangkat', $cari)
                    ->orWhereHas('paket', function ($p) use ($cari) {
                        $p->where('nama_paket', 'like', "%{$cari}%")
                            ->orWhereHas('destinasi', fn ($d) => $d->where('nama_destinasi', 'like', "%{$cari}%")->orWhere('lokasi', 'like', "%{$cari}%"));
                    })
                    ->orWhereHas('penugasan.petugas', fn ($pt) => $pt->where('nama_petugas', 'like', "%{$cari}%"));
            });
        }

        // 2. Filter Paket Wisata Tertentu
        if ($request->filled('paket')) {
            $query->where('id_paket', $request->integer('paket'));
        }

        // 3. Filter Tanggal Spesifik
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_berangkat', $request->input('tanggal'));
        }

        // 4. Filter Status Jadwal
        if ($status = $request->input('status')) {
            match ($status) {
                'mendatang' => $query->where('tanggal_berangkat', '>=', now()->toDateString()),
                'lewat' => $query->where('tanggal_berangkat', '<', now()->toDateString()),
                'tersedia' => $query->where('sisa_kuota', '>', 0),
                'penuh' => $query->where('sisa_kuota', '<=', 0),
                default => null,
            };
        }

        $jadwal = $query->orderByDesc('tanggal_berangkat')
            ->paginate(12)
            ->withQueryString();

        foreach ($jadwal as $j) {
            $j->sinkronSisaKuota();
        }

        return view('admin.jadwal.index', [
            'jadwal' => $jadwal,
            'daftarPaket' => PaketWisata::orderBy('nama_paket')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.jadwal.form', [
            'jadwal' => new JadwalKeberangkatan(['tanggal_berangkat' => now()->addWeek()]),
            'daftarPaket' => PaketWisata::where('is_active', true)->orderBy('nama_paket')->get(),
            'petugas' => Petugas::orderBy('nama_petugas')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['sisa_kuota'] = $data['kuota_peserta'];

        $jadwal = JadwalKeberangkatan::create($data);
        $this->sinkronTitikJemput($jadwal, $request);
        $this->simpanPenugasan($request, $jadwal);

        return redirect()->route('admin.jadwal.index')->with('sukses', 'Jadwal keberangkatan berhasil ditambahkan.');
    }

    public function edit(JadwalKeberangkatan $jadwal)
    {
        return view('admin.jadwal.form', [
            'jadwal' => $jadwal,
            'daftarPaket' => PaketWisata::orderBy('nama_paket')->get(),
            'petugas' => Petugas::orderBy('nama_petugas')->get(),
        ]);
    }

    public function update(Request $request, JadwalKeberangkatan $jadwal)
    {
        $data = $this->validasi($request);

        $terpakai = $jadwal->kuotaTerpakai();

        if ($data['kuota_peserta'] < $terpakai) {
            throw ValidationException::withMessages([
                'kuota_peserta' => "Kuota tidak boleh lebih kecil dari peserta aktif ({$terpakai} orang).",
            ]);
        }

        $jadwal->update($data);
        $this->sinkronTitikJemput($jadwal, $request);
        $jadwal->sinkronSisaKuota();
        $this->simpanPenugasan($request, $jadwal);

        return redirect()->route('admin.jadwal.index')->with('sukses', 'Jadwal keberangkatan berhasil diperbarui.');
    }

    public function destroy(JadwalKeberangkatan $jadwal)
    {
        if ($jadwal->pemesanan()->whereIn('status_pemesanan', ['terkonfirmasi', 'menunggu_verifikasi'])->exists()) {
            return back()->withErrors(['jadwal' => 'Jadwal sudah memiliki pemesanan aktif dan tidak dapat dihapus.']);
        }

        $jadwal->delete();

        return back()->with('sukses', 'Jadwal keberangkatan dihapus.');
    }

    private function simpanPenugasan(Request $request, JadwalKeberangkatan $jadwal): void
    {
        $ids = collect($request->input('petugas', []))->filter()->map(fn ($id) => (int) $id)->all();

        Penugasan::where('id_jadwal', $jadwal->id)->whereNotIn('id_petugas', $ids ?: [0])->delete();

        foreach ($ids as $idPetugas) {
            Penugasan::updateOrCreate(
                ['id_jadwal' => $jadwal->id, 'id_petugas' => $idPetugas],
                ['tanggal_tugas' => $jadwal->tanggal_berangkat]
            );
        }
    }

    /**
     * Samakan daftar titik jemput dengan isian form.
     * Titik yang masih dipakai pemesanan tidak dihapus.
     */
    private function sinkronTitikJemput(JadwalKeberangkatan $jadwal, Request $request): void
    {
        $baris = collect($request->input('titik', []))
            ->filter(fn ($t) => filled($t['nama'] ?? null))
            ->values();

        $idDipakai = \App\Models\Pemesanan::where('id_jadwal', $jadwal->id)
            ->whereNotNull('id_titik_jemput')->pluck('id_titik_jemput')->all();

        $tersimpan = [];
        foreach ($baris as $i => $t) {
            $data = [
                'nama' => $t['nama'],
                'jam' => filled($t['jam'] ?? null) ? $t['jam'] : null,
                'urutan' => $i + 1,
                'biaya_tambahan' => (float) ($t['biaya_tambahan'] ?? 0),
            ];

            $id = $t['id'] ?? null;
            if ($id && $jadwal->titikJemput()->whereKey($id)->exists()) {
                $jadwal->titikJemput()->whereKey($id)->update($data);
                $tersimpan[] = (int) $id;
            } else {
                $tersimpan[] = $jadwal->titikJemput()->create($data)->id;
            }
        }

        // Titik yang tidak lagi ada di form tapi masih dipakai pemesanan:
        // jangan dihapus, cukup dipindah ke urutan akhir agar tidak tampil sebagai pilihan utama.
        $sisa = $jadwal->titikJemput()->whereNotIn('id', $tersimpan ?: [0])->pluck('id')->all();
        $dipakai = array_values(array_intersect($sisa, $idDipakai));
        if ($dipakai) {
            \App\Models\TitikJemput::whereIn('id', $dipakai)->update(['urutan' => 99]);
        }

        $hapus = array_values(array_diff($sisa, $idDipakai));
        if ($hapus) {
            \App\Models\TitikJemput::whereIn('id', $hapus)->delete();
        }
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'id_paket' => ['required', 'exists:paket_wisata,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_berangkat'],
            'kuota_peserta' => ['required', 'integer', 'min:1', 'max:200'],
            'kota_kumpul' => ['nullable', 'string', 'max:80'],
            'titik_kumpul' => ['nullable', 'string', 'max:160'],
            'jam_kumpul' => ['nullable', 'date_format:H:i'],
            'catatan_keberangkatan' => ['nullable', 'string', 'max:500'],
            'titik' => ['nullable', 'array'],
            'titik.*.nama' => ['nullable', 'string', 'max:120'],
            'titik.*.jam' => ['nullable', 'date_format:H:i'],
            'titik.*.biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
        ], [
            'id_paket.required' => 'Paket wajib dipilih.',
            'tanggal_berangkat.required' => 'Tanggal berangkat wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal berangkat.',
            'kuota_peserta.required' => 'Kuota peserta wajib diisi.',
        ]);
    }
}
