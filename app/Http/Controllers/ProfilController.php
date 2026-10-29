<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        return view('profil.edit', compact('user', 'statistik'));
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

        return back()->with('sukses', 'Kata sandi berhasil diperbarui.');
    }
}
