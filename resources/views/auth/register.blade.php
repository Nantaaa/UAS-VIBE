@extends('layouts.app')

@section('title', 'Daftar Akun')
@section('minimal', 'true')

@section('content')
<div class="container auth-shell">
    <div class="auth-wrap">
        <section class="hero-card auth-hero">
            @include('partials.auth-hero')
        </section>

        <div>
            @include('partials.auth-panel', ['mode' => 'daftar'])

            @include('partials.demo-akun')
        </div>
    </div>
</div>
@endsection
