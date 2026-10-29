@extends('layouts.app')

@section('title', 'Wishlist')

@section('content')
<div class="container" style="padding-top:26px">
    <div class="row between wrap" style="gap:12px">
        <div>
            <h1 style="font-size:23px;margin:0 0 4px">Wishlist Saya</h1>
            <p class="muted mb-0" style="font-size:12.5px">Paket wisata yang Anda simpan untuk dipertimbangkan nanti.</p>
        </div>
        <a href="{{ route('beranda') }}#katalog" class="btn btn-ghost btn-sm">Jelajahi paket lain</a>
    </div>

    @if($items->isEmpty())
        <div class="card empty mt-16">
            <div class="big">@include('partials.icon', ['name' => 'heart', 'size' => 34])</div>
            <h3>Wishlist masih kosong</h3>
            <p class="mb-0">Tekan ikon hati pada kartu paket untuk menyimpannya di sini.</p>
            <a href="{{ route('beranda') }}#katalog" class="btn btn-primary mt-16">Lihat Paket Wisata</a>
        </div>
    @else
        <div class="grid grid-3 mt-16">
            @foreach($items as $w)
                @php($p = $w->paket)
                @if(! $p) @continue @endif
                <article class="katalog-card">
                    <a href="{{ route('paket.detail', $p) }}" class="katalog-thumb">
                        @if($p->foto)<img src="{{ '/storage/'.$p->foto }}" alt="{{ $p->nama_paket }}">@endif
                        <span class="thumb-shade"></span>
                        @if($p->is_bestseller)<span class="badge-seller">Best Seller</span>@endif
                        <span class="pill-tersedia">● Tersedia</span>
                    </a>

                    <form method="POST" action="{{ route('wishlist.toggle', $p) }}" class="heart-form">
                        @csrf
                        <button type="submit" class="heart active" title="Hapus dari wishlist">
                            @include('partials.icon', ['name' => 'heart', 'size' => 15])
                        </button>
                    </form>

                    <div class="katalog-body">
                        <a href="{{ route('paket.detail', $p) }}" class="katalog-title">{{ $p->nama_paket }}</a>
                        <div class="katalog-loc">@include('partials.icon', ['name' => 'map-pin', 'size' => 12]) {{ $p->destinasi->lokasi }}</div>
                        <div class="katalog-meta">{{ $p->highlight }}</div>
                        <div class="katalog-price">
                            <div>
                                <div class="now">Rp {{ number_format($p->tarif, 0, ',', '.') }}</div>
                                @if($p->harga_asli)<div class="was">Rp {{ number_format($p->harga_asli, 0, ',', '.') }}</div>@endif
                            </div>
                            <a href="{{ route('paket.detail', $p) }}" class="btn btn-soft btn-sm">Detail</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            @if($items->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $items->previousPageUrl() }}">‹</a> @endif
            @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($items->hasMorePages()) <a href="{{ $items->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
        </div>
    @endif
</div>
@endsection
