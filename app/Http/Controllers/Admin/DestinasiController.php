<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinasiController extends Controller
{
    public function index()
    {
        $destinasi = Destinasi::withCount('paket')->orderBy('nama_destinasi')->paginate(10);

        return view('admin.destinasi.index', compact('destinasi'));
    }

    public function create()
    {
        return view('admin.destinasi.form', ['destinasi' => new Destinasi]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('destinasi', 'public');
        }

        Destinasi::create($data);

        return redirect()->route('admin.destinasi.index')->with('sukses', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(Destinasi $destinasi)
    {
        return view('admin.destinasi.form', compact('destinasi'));
    }

    public function update(Request $request, Destinasi $destinasi)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            if ($destinasi->foto) {
                Storage::disk('public')->delete($destinasi->foto);
            }
            $data['foto'] = $request->file('foto')->store('destinasi', 'public');
        }

        $destinasi->update($data);

        return redirect()->route('admin.destinasi.index')->with('sukses', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Destinasi $destinasi)
    {
        if ($destinasi->paket()->exists()) {
            return back()->withErrors(['destinasi' => 'Destinasi masih dipakai pada paket wisata dan tidak dapat dihapus.']);
        }

        if ($destinasi->foto) {
            Storage::disk('public')->delete($destinasi->foto);
        }

        $destinasi->delete();

        return back()->with('sukses', 'Destinasi berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_destinasi' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'nama_destinasi.required' => 'Nama destinasi wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 3 MB.',
        ]);
    }
}
