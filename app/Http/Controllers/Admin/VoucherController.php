<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct(private VoucherService $voucher) {}

    public function index()
    {
        return view('admin.voucher.index', ['hasil' => null, 'kode' => null]);
    }

    public function validasi(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:100'],
        ], [
            'kode.required' => 'Kode pemesanan atau kode QR wajib diisi.',
        ]);

        $hasil = $this->voucher->validasi($data['kode']);

        return view('admin.voucher.index', ['hasil' => $hasil, 'kode' => $data['kode']]);
    }
}
