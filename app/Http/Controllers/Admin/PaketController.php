<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $data['is_active'] = $request->boolean('is_active');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        $data['include_penerbangan'] = $request->boolean('include_penerbangan');
        $data['harga_asli'] = $request->filled('harga_asli') ? $request->input('harga_asli') : null;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('paket', 'public');
        }

        PaketWisata::create($data);

        $pesan = isset($destinasiBaru)
            ? 'Paket wisata dan destinasi baru "' . $destinasiBaru->nama_destinasi . '" berhasil ditambahkan.'
            : 'Paket wisata berhasil ditambahkan.';

        return redirect()->route('admin.paket.index')->with('sukses', $pesan);
    }

    public function edit(PaketWisata $paket)
    {
        return view('admin.paket.form', [
            'paket' => $paket,
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

        return redirect()->route('admin.paket.index')->with('sukses', 'Paket wisata berhasil diperbarui.');
    }

    public function destroy(PaketWisata $paket)
    {
        if ($paket->jadwal()->whereHas('pemesanan', fn ($q) => $q->whereIn('status_pemesanan', ['terkonfirmasi']))->exists()) {
            return back()->withErrors(['paket' => 'Paket memiliki pemesanan terkonfirmasi. Nonaktifkan saja, jangan dihapus.']);
        }

        $paket->update(['is_active' => false]);

        return back()->with('sukses', 'Paket wisata dinonaktifkan.');
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
