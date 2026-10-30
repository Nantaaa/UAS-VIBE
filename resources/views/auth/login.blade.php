@extends('layouts.app')

@section('title', 'Masuk')
@section('minimal', 'true')

@section('content')
<div class="container auth-shell">
    <div class="auth-wrap">
        <section class="hero-card auth-hero">
            @include('partials.auth-hero')
        </section>

        <div>
            @include('partials.auth-panel', ['mode' => 'masuk'])

            @include('partials.demo-akun')
        </div>
    </div>
</div>
@endsection
