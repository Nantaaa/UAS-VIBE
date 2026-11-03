<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $statistik = [
            'total' => Pemesanan::where('id_user', $user->id)->count(),
            'terkonfirmasi' => Pemesanan::where('id_user', $user->id)->where('status_pemesanan', 'terkonfirmasi')->count(),
            'perjalanan' => (int) Pemesanan::where('id_user', $user->id)->where('status_pemesanan', 'terkonfirmasi')->sum('jumlah_peserta'),
        ];

        $riwayatPembayaran = Pembayaran::whereHas('pemesanan', fn ($q) => $q->where('id_user', $user->id))
            ->with(['pemesanan.jadwal.paket.destinasi'])
            ->latest('tanggal_upload')
            ->get();

        $riwayatPeserta = Peserta::whereHas('pemesanan', fn ($q) => $q->where('id_user', $user->id))
            ->latest('created_at')
            ->limit(6)
            ->get();

        return view('profil.edit', compact('user', 'statistik', 'riwayatPembayaran', 'riwayatPeserta'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['required', 'string', 'min:9', 'max:15', 'regex:/^[0-9+]+$/'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'kewarganegaraan' => ['nullable', 'string', 'max:50'],
            'no_identitas' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak valid.',
        ]);

        $user->update($data);

        return back()->with('sukses', 'Informasi profil berhasil diperbarui.');
    }

    public function updateDokumen(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'jenis_identitas' => ['required', 'in:KTP,Paspor'],
            'no_identitas' => ['required', 'string', 'max:30'],
            'kewarganegaraan' => ['nullable', 'string', 'max:50'],
            'file_identitas' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'no_identitas.required' => 'Nomor identitas (NIK / Paspor) wajib diisi.',
            'file_identitas.mimes' => 'Berkas identitas harus berformat JPG, PNG, atau PDF.',
            'file_identitas.max' => 'Ukuran berkas identitas maksimal 5 MB.',
        ]);

        if ($request->hasFile('file_identitas')) {
            if ($user->file_identitas && Storage::disk('local')->exists($user->file_identitas)) {
                Storage::disk('local')->delete($user->file_identitas);
            }
            $data['file_identitas'] = $request->file('file_identitas')->store('identitas', 'local');
        }

        $user->update($data);

        return redirect()->to(route('profil.edit').'#dokumen')->with('sukses', 'Dokumen identitas berhasil disimpan.');
    }

    public function lihatBerkas(Request $request)
    {
        $user = $request->user();
        abort_unless($user->file_identitas && Storage::disk('local')->exists($user->file_identitas), 404);

        return response()->file(Storage::disk('local')->path($user->file_identitas));
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required' => 'Kata sandi saat ini wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        if (! Hash::check($data['password_lama'], $request->user()->password_hash)) {
            return back()->withErrors(['password_lama' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $request->user()->update(['password_hash' => $data['password']]);

        return redirect()->to(route('profil.edit').'#keamanan')->with('sukses', 'Kata sandi berhasil diperbarui.');
    }
}
