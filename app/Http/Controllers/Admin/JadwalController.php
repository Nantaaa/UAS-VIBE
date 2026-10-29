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
        $jadwal = JadwalKeberangkatan::with(['paket.destinasi', 'penugasan.petugas'])
            ->withCount(['pemesanan as peserta_terkonfirmasi' => fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi')])
            ->when($request->filled('paket'), fn ($q) => $q->where('id_paket', $request->integer('paket')))
            ->orderByDesc('tanggal_berangkat')
            ->paginate(10)
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

    private function validasi(Request $request): array
    {
        return $request->validate([
            'id_paket' => ['required', 'exists:paket_wisata,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_berangkat'],
            'kuota_peserta' => ['required', 'integer', 'min:1', 'max:200'],
        ], [
            'id_paket.required' => 'Paket wajib dipilih.',
            'tanggal_berangkat.required' => 'Tanggal berangkat wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal berangkat.',
            'kuota_peserta.required' => 'Kuota peserta wajib diisi.',
        ]);
    }
}
