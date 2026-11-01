<?php

namespace App\Http\Controllers;

use App\Models\JadwalKeberangkatan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingService;
use App\Services\PromoService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PemesananController extends Controller
{
    public function __construct(
        private BookingService $booking,
        private VoucherService $voucher,
        private PromoService $promo,
    ) {}

    public function cekPromo(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30'],
            'id_jadwal' => ['required', 'integer', 'exists:jadwal_keberangkatan,id'],
            'jumlah' => ['nullable', 'integer', 'min:2', 'max:20'],
            'dewasa' => ['nullable', 'integer', 'min:1', 'max:20'],
            'anak' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $jadwal = \App\Models\JadwalKeberangkatan::with('paket')->findOrFail($data['id_jadwal']);
        $tarifDewasa = (float) $jadwal->paket->tarif;
        $dewasa = (int) ($data['dewasa'] ?? $data['jumlah'] ?? 2);
        $anak = (int) ($data['anak'] ?? 0);
        if ($dewasa + $anak < 2) {
            $dewasa = 2;
        }
        $subtotal = ($dewasa * $tarifDewasa) + ($anak * \App\Models\Pemesanan::tarifKategori($tarifDewasa, 'anak'));

        try {
            $hasil = $this->promo->hitung($data['kode'], $subtotal);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'pesan' => collect($e->errors())->flatten()->first(),
                'subtotal' => $subtotal,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'pesan' => 'Kode promo diterapkan: '.$hasil['promo']->kode.' (-'.$hasil['promo']->labelDiskon().')',
            'kode' => $hasil['promo']->kode,
            'diskon' => (float) $hasil['diskon'],
            'subtotal' => $subtotal,
            'total' => (float) $hasil['total'],
        ]);
    }

    public function form(JadwalKeberangkatan $jadwal)
    {
        $jadwal->load('paket.destinasi');

        if ($jadwal->tanggal_berangkat->isPast()) {
            return redirect()->route('paket.detail', $jadwal->id_paket)
                ->withErrors(['jadwal' => 'Jadwal ini sudah lewat.']);
        }

        $jadwal->sinkronSisaKuota();

        if ($jadwal->sisa_kuota < 1) {
            return redirect()->route('paket.detail', $jadwal->id_paket)
                ->withErrors(['jadwal' => 'Kuota jadwal ini sudah penuh.']);
        }

        return view('pemesanan.form', compact('jadwal'));
    }

    public function store(Request $request, JadwalKeberangkatan $jadwal)
    {
        $request->validate([
            'jumlah_peserta' => ['required', 'integer', 'min:2', 'max:20'],
            'dewasa' => ['nullable', 'integer', 'min:1', 'max:20'],
            'anak' => ['nullable', 'integer', 'min:0', 'max:10'],
            'id_titik_jemput' => ['nullable', 'integer', 'exists:titik_jemput,id'],
            'peserta' => ['required', 'array'],
            'peserta.*.nama_peserta' => ['required', 'string', 'max:100'],
            'peserta.*.jenis_identitas' => ['required', 'in:KTP,Paspor'],
            'peserta.*.no_identitas' => ['required', 'string', 'max:30'],
            'peserta.*.file_identitas' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Pemesanan paket wisata minimal 2 peserta.',
            'dewasa.min' => 'Minimal 1 peserta dewasa harus disertakan.',
            'peserta.*.nama_peserta.required' => 'Nama peserta wajib diisi.',
            'peserta.*.no_identitas.required' => 'Nomor identitas wajib diisi.',
            'peserta.*.file_identitas.required' => 'Berkas KTP/Paspor wajib diunggah.',
            'peserta.*.file_identitas.mimes' => 'Berkas identitas harus JPG, PNG, atau PDF.',
            'peserta.*.file_identitas.max' => 'Ukuran berkas identitas maksimal 2 MB.',
        ]);

        // Kategori peserta: dewasa & anak dihitung terpisah karena tarifnya berbeda
        $dewasa = (int) $request->input('dewasa', $request->input('jumlah_peserta'));
        $anak = (int) $request->input('anak', 0);

        if ($dewasa + $anak < 2) {
            throw ValidationException::withMessages([
                'jumlah_peserta' => 'Pemesanan paket wisata minimal 2 peserta.',
            ]);
        }
        if ($dewasa < 1) {
            throw ValidationException::withMessages([
                'dewasa' => 'Pemesanan wajib menyertakan minimal 1 peserta dewasa.',
            ]);
        }

        $jumlahPeserta = $dewasa + $anak;

        $input = $request->input('peserta', []);
        $files = $request->file('peserta', []);

        $data = [];
        foreach ($input as $i => $row) {
            $row['file_identitas'] = $files[$i]['file_identitas'] ?? null;
            $data[$i] = $row;
        }

        $data = array_slice($data, 0, $jumlahPeserta, true);

        if (count($data) < $jumlahPeserta) {
            throw ValidationException::withMessages([
                'peserta' => 'Data peserta belum lengkap sesuai jumlah peserta.',
            ]);
        }

        $urut = 0;
        foreach (array_keys($data) as $key) {
            $data[$key]['kategori'] = $urut < $dewasa ? 'dewasa' : 'anak';
            $urut++;
        }

        foreach ($data as $row) {
            if (! $row['file_identitas'] instanceof \Illuminate\Http\UploadedFile) {
                throw ValidationException::withMessages([
                    'peserta' => 'Berkas KTP/Paspor setiap peserta wajib diunggah.',
                ]);
            }
        }

        $tarifDewasa = (float) $jadwal->paket->tarif;
        $subtotal = ($dewasa * $tarifDewasa) + ($anak * \App\Models\Pemesanan::tarifKategori($tarifDewasa, 'anak'));

        // biaya jemput ikut dihitung sebagai bagian tagihan sebelum diskon
        if ($request->filled('id_titik_jemput')) {
            $titik = \App\Models\TitikJemput::where('id_jadwal', $jadwal->id)
                ->find($request->input('id_titik_jemput'));
            if ($titik) {
                $subtotal += (float) $titik->biaya_tambahan;
            }
        }

        $promo = $this->promo->hitung($request->input('kode_promo'), $subtotal);

        $pemesanan = $this->booking->buat(
            $request->user(),
            $jadwal->id,
            array_values($data),
            $promo,
            $dewasa,
            $anak,
            $request->filled('id_titik_jemput') ? (int) $request->input('id_titik_jemput') : null
        );

        if ($promo['promo']) {
            $this->promo->tandaiTerpakai($promo['promo']);
        }

        return redirect()->route('pemesanan.show', $pemesanan)
            ->with('sukses', 'Pemesanan berhasil dibuat. Selesaikan pembayaran dalam 1 jam.');
    }

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'semua');

        $daftar = Pemesanan::with(['jadwal.paket.destinasi', 'tiket', 'pembayaranTerakhir'])
            ->where('id_user', $request->user()->id)
            ->when($tab === 'menunggu_pembayaran', fn ($q) => $q->where('status_pemesanan', 'menunggu_pembayaran'))
            ->when($tab === 'diproses', fn ($q) => $q->whereIn('status_pemesanan', ['menunggu_verifikasi', 'perlu_perbaikan']))
            ->when($tab === 'selesai', fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi'))
            ->when($tab === 'dibatalkan', fn ($q) => $q->whereIn('status_pemesanan', ['dibatalkan', 'kedaluwarsa']))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $cari = $request->input('q');
                $w->where('kode_pemesanan', 'like', "%{$cari}%")
                    ->orWhereHas('jadwal.paket', fn ($p) => $p->where('nama_paket', 'like', "%{$cari}%"));
            }))
            ->when($request->filled('urut'), fn ($q) => $request->input('urut') === 'terlama'
                ? $q->oldest('tanggal_pemesanan')
                : $q->latest('tanggal_pemesanan'), fn ($q) => $q->latest('tanggal_pemesanan'))
            ->paginate(6)
            ->withQueryString();

        $hitung = [
            'semua' => Pemesanan::where('id_user', $request->user()->id)->count(),
            'menunggu_pembayaran' => Pemesanan::where('id_user', $request->user()->id)->where('status_pemesanan', 'menunggu_pembayaran')->count(),
            'diproses' => Pemesanan::where('id_user', $request->user()->id)->whereIn('status_pemesanan', ['menunggu_verifikasi', 'perlu_perbaikan'])->count(),
            'selesai' => Pemesanan::where('id_user', $request->user()->id)->where('status_pemesanan', 'terkonfirmasi')->count(),
            'dibatalkan' => Pemesanan::where('id_user', $request->user()->id)->whereIn('status_pemesanan', ['dibatalkan', 'kedaluwarsa'])->count(),
        ];

        return view('pemesanan.index', compact('daftar', 'tab', 'hitung'));
    }

    public function show(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        $pemesanan->load(['jadwal.paket.destinasi', 'peserta', 'pembayaran', 'tiket']);

        return view('pemesanan.show', compact('pemesanan'));
    }

    public function pembayaran(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        $pemesanan->load(['jadwal.paket.destinasi', 'pembayaran']);

        return view('pemesanan.pembayaran', compact('pemesanan'));
    }

    public function unggahPembayaran(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        if (! in_array($pemesanan->status_pemesanan, ['menunggu_pembayaran', 'perlu_perbaikan'], true)) {
            return back()->withErrors(['bukti' => 'Pemesanan ini tidak sedang menunggu pembayaran.']);
        }

        if ($pemesanan->status_pemesanan === 'menunggu_pembayaran' && $pemesanan->sisaDetikPembayaran() <= 0) {
            $pemesanan->update([
                'status_pemesanan' => 'kedaluwarsa',
                'catatan_status' => 'Batas waktu pembayaran 1 jam terlewati.',
            ]);
            $pemesanan->jadwal->sinkronSisaKuota();

            return back()->withErrors(['bukti' => 'Batas waktu pembayaran sudah lewat. Pemesanan kedaluwarsa.']);
        }

        $request->validate([
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'bukti.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti.mimes' => 'Bukti pembayaran harus JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
        ]);

        $path = $request->file('bukti')->store('pembayaran', 'local');

        Pembayaran::create([
            'id_pemesanan' => $pemesanan->id,
            'jumlah_bayar' => $pemesanan->total_bayar,
            'bukti_pembayaran' => $path,
            'tanggal_upload' => now(),
            'status_verifikasi' => 'menunggu',
        ]);

        $pemesanan->update([
            'status_pemesanan' => 'menunggu_verifikasi',
            'catatan_status' => 'Bukti pembayaran diterima. Menunggu verifikasi Admin Travel.',
        ]);

        return back()->with('sukses', 'Bukti pembayaran terkirim. Menunggu verifikasi admin.');
    }

    public function batal(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        $this->booking->batalkan($pemesanan);

        return back()->with('sukses', 'Pemesanan dibatalkan dan kuota dilepas.');
    }

    public function perbaruiIdentitasPeserta(Request $request, Pemesanan $pemesanan, \App\Models\Peserta $peserta)
    {
        $this->authorizeOwner($request, $pemesanan);

        if ($peserta->id_pemesanan !== $pemesanan->id) {
            abort(404);
        }

        $request->validate([
            'file_identitas' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'no_identitas' => ['nullable', 'string', 'max:30'],
        ], [
            'file_identitas.required' => 'Berkas identitas pengganti wajib diunggah.',
            'file_identitas.mimes' => 'Format berkas harus JPG, PNG, atau PDF.',
            'file_identitas.max' => 'Ukuran berkas maksimal 2 MB.',
        ]);

        $path = $request->file('file_identitas')->store('identitas', 'local');

        $peserta->update([
            'file_identitas' => $path,
            'no_identitas' => $request->input('no_identitas', $peserta->no_identitas),
            'status' => 'menunggu',
        ]);

        if ($pemesanan->status_pemesanan === 'perlu_perbaikan') {
            $pemesanan->update([
                'status_pemesanan' => 'menunggu_verifikasi',
                'catatan_status' => 'Dokumen peserta '.$peserta->nama_peserta.' telah diperbarui oleh wisatawan.',
            ]);
        }

        return back()->with('sukses', 'Dokumen identitas peserta '.$peserta->nama_peserta.' berhasil diperbarui.');
    }

    public function unduhVoucher(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        if ($pemesanan->status_pemesanan !== 'terkonfirmasi') {
            return back()->withErrors(['voucher' => 'Voucher hanya tersedia untuk pemesanan terkonfirmasi.']);
        }

        $tiket = $pemesanan->tiket ?: $this->voucher->terbitkan($pemesanan);

        if (! $tiket->file_pdf || ! Storage::disk('local')->exists($tiket->file_pdf)) {
            $this->voucher->buatPdf($pemesanan, $tiket);
            $tiket->refresh();
        }

        return Storage::disk('local')->download(
            $tiket->file_pdf,
            'voucher-'.$pemesanan->kode_pemesanan.'.pdf'
        );
    }

    public function lihatVoucher(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        abort_unless($pemesanan->status_pemesanan === 'terkonfirmasi', 403);

        $pemesanan->load(['jadwal.paket.destinasi', 'peserta', 'user', 'tiket']);

        return view('pemesanan.voucher', compact('pemesanan'));
    }

    public function berkas(Request $request, Pemesanan $pemesanan, string $jenis, int $id)
    {
        $this->authorizeOwner($request, $pemesanan, allowAdmin: true);

        if ($jenis === 'identitas') {
            $peserta = $pemesanan->peserta()->findOrFail($id);
            $path = $peserta->file_identitas;
        } else {
            $bayar = $pemesanan->pembayaran()->findOrFail($id);
            $path = $bayar->bukti_pembayaran;
        }

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function authorizeOwner(Request $request, Pemesanan $pemesanan, bool $allowAdmin = false): void
    {
        $user = $request->user();

        if ($pemesanan->id_user !== $user->id && ! ($allowAdmin && $user->isAdmin())) {
            abort(403, 'Anda tidak berhak mengakses pemesanan ini.');
        }
    }
}
