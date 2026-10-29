<?php

namespace App\Http\Controllers;

use App\Models\JadwalKeberangkatan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PemesananController extends Controller
{
    public function __construct(
        private BookingService $booking,
        private VoucherService $voucher,
    ) {}

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
            'jumlah_peserta' => ['required', 'integer', 'min:1', 'max:20'],
            'peserta' => ['required', 'array'],
            'peserta.*.nama_peserta' => ['required', 'string', 'max:100'],
            'peserta.*.jenis_identitas' => ['required', 'in:KTP,Paspor'],
            'peserta.*.no_identitas' => ['required', 'string', 'max:30'],
            'peserta.*.file_identitas' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'peserta.*.nama_peserta.required' => 'Nama peserta wajib diisi.',
            'peserta.*.no_identitas.required' => 'Nomor identitas wajib diisi.',
            'peserta.*.file_identitas.required' => 'Berkas KTP/Paspor wajib diunggah.',
            'peserta.*.file_identitas.mimes' => 'Berkas identitas harus JPG, PNG, atau PDF.',
            'peserta.*.file_identitas.max' => 'Ukuran berkas identitas maksimal 2 MB.',
        ]);

        $input = $request->input('peserta', []);
        $files = $request->file('peserta', []);

        $data = [];
        foreach ($input as $i => $row) {
            $row['file_identitas'] = $files[$i]['file_identitas'] ?? null;
            $data[$i] = $row;
        }

        $data = array_slice($data, 0, (int) $request->input('jumlah_peserta'), true);

        if (count($data) < (int) $request->input('jumlah_peserta')) {
            throw ValidationException::withMessages([
                'peserta' => 'Data peserta belum lengkap sesuai jumlah peserta.',
            ]);
        }

        foreach ($data as $row) {
            if (! $row['file_identitas'] instanceof \Illuminate\Http\UploadedFile) {
                throw ValidationException::withMessages([
                    'peserta' => 'Berkas KTP/Paspor setiap peserta wajib diunggah.',
                ]);
            }
        }

        $pemesanan = $this->booking->buat($request->user(), $jadwal->id, array_values($data));

        return redirect()->route('pemesanan.show', $pemesanan)
            ->with('sukses', 'Pemesanan berhasil dibuat. Selesaikan pembayaran dalam 1 jam.');
    }

    public function index(Request $request)
    {
        $daftar = Pemesanan::with(['jadwal.paket.destinasi', 'tiket'])
            ->where('id_user', $request->user()->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status_pemesanan', $request->input('status')))
            ->latest('tanggal_pemesanan')
            ->paginate(8)
            ->withQueryString();

        return view('pemesanan.index', compact('daftar'));
    }

    public function show(Request $request, Pemesanan $pemesanan)
    {
        $this->authorizeOwner($request, $pemesanan);

        $pemesanan->load(['jadwal.paket.destinasi', 'peserta', 'pembayaran', 'tiket']);

        return view('pemesanan.show', compact('pemesanan'));
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
