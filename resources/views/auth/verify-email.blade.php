@extends('layouts.guest')

@section('title', 'Verifica tu correo - HAXX COORP')

@section('content')
    <div>
        <h1 class="hero-title">Confirma tu correo</h1>
        <p class="hero-subtitle">Antes de entrar, valida que tu correo es real desde el enlace enviado.</p>
    </div>

    <div class="glass-card" style="padding:.95rem; border-radius:14px;">
        <p style="margin:0; color:var(--muted);">
            Revisa tu bandeja principal y spam. Cuando confirmes, vuelve aquí y presiona continuar.
        </p>
    </div>

    <form method="post" action="{{ route('verification.send') }}" class="form-grid">
        @csrf
        <button type="submit" class="btn btn-outline">Reenviar correo de confirmación</button>
    </form>

    <a href="{{ route('login') }}" class="auth-link">Volver al inicio de sesión</a>
@endsection
