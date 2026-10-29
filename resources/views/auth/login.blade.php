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

            <div class="card card-pad mt-16">
                <div class="tiny muted mb-0">
                    <strong style="color:var(--ink)">Akun demo:</strong>
                    admin@nanta.id · wisatawan@nanta.id — kata sandi <strong>password</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
