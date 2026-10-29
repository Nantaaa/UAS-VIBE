<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function __construct(private VoucherService $voucher) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'menunggu_verifikasi');

        $daftar = Pemesanan::with(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaranTerakhir'])
            ->whereIn('status_pemesanan', $status === 'perlu_perbaikan'
                ? ['perlu_perbaikan']
                : ['menunggu_verifikasi'])
            ->orderBy('tanggal_pemesanan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.index', [
            'daftar' => $daftar,
            'status' => $status,
            'jumlahMenunggu' => Pemesanan::where('status_pemesanan', 'menunggu_verifikasi')->count(),
            'jumlahPerbaikan' => Pemesanan::where('status_pemesanan', 'perlu_perbaikan')->count(),
        ]);
    }

    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaran']);

        return view('admin.verifikasi.show', compact('pemesanan'));
    }

    public function setujui(Request $request, Pemesanan $pemesanan)
    {
        if ($pemesanan->status_pemesanan !== 'menunggu_verifikasi') {
            return back()->withErrors(['status' => 'Hanya pemesanan berstatus Menunggu Verifikasi yang dapat dikonfirmasi.']);
        }

        DB::transaction(function () use ($request, $pemesanan) {
            $pembayaran = $pemesanan->pembayaran()->latest('tanggal_upload')->first();

            $pembayaran?->update([
                'status_verifikasi' => 'valid',
                'id_admin_verifikator' => $request->user()->id,
            ]);

            $pemesanan->peserta()->update(['status' => 'valid']);

            $pemesanan->update([
                'status_pemesanan' => 'terkonfirmasi',
                'catatan_status' => 'Dokumen dan pembayaran sesuai. Pemesanan terkonfirmasi, voucher telah diterbitkan.',
            ]);

            $this->voucher->terbitkan($pemesanan);
        });

        return redirect()->route('admin.verifikasi.index')
            ->with('sukses', 'Pemesanan dikonfirmasi dan voucher diterbitkan.');
    }

    public function tolak(Request $request, Pemesanan $pemesanan)
    {
        $data = $request->validate([
            'alasan' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'alasan.required' => 'Alasan perbaikan wajib diisi agar wisatawan tahu apa yang harus diperbaiki.',
            'alasan.min' => 'Alasan perbaikan terlalu singkat.',
        ]);

        if (! in_array($pemesanan->status_pemesanan, ['menunggu_verifikasi', 'perlu_perbaikan'], true)) {
            return back()->withErrors(['status' => 'Status pemesanan tidak dapat dikembalikan untuk perbaikan.']);
        }

        DB::transaction(function () use ($request, $pemesanan, $data) {
            $pembayaran = $pemesanan->pembayaran()->latest('tanggal_upload')->first();

            $pembayaran?->update([
                'status_verifikasi' => 'tidak_valid',
                'id_admin_verifikator' => $request->user()->id,
            ]);

            $pemesanan->peserta()->update(['status' => 'perlu_perbaikan']);

            $pemesanan->update([
                'status_pemesanan' => 'perlu_perbaikan',
                'catatan_status' => $data['alasan'],
            ]);
        });

        return redirect()->route('admin.verifikasi.index')
            ->with('sukses', 'Pemesanan dikembalikan ke wisatawan untuk perbaikan.');
    }
}
