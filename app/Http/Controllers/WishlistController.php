<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = Wishlist::with(['paket.destinasi', 'paket.jadwalTersedia'])
            ->where('id_user', $request->user()->id)
            ->latest()
            ->paginate(9);

        return view('wishlist.index', compact('items'));
    }

    public function toggle(Request $request, PaketWisata $paket)
    {
        $userId = $request->user()->id;

        $ada = Wishlist::where('id_user', $userId)->where('id_paket', $paket->id)->first();

        if ($ada) {
            $ada->delete();
            $pesan = 'Dihapus dari wishlist.';
        } else {
            Wishlist::create(['id_user' => $userId, 'id_paket' => $paket->id]);
            $pesan = 'Ditambahkan ke wishlist.';
        }

        return back()->with('sukses', $pesan);
    }
}
