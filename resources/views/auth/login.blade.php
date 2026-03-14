@extends('layouts.guest')

@section('title', 'Iniciar sesión - HAXX COORP')

@section('content')
    <div>
        <h1 class="hero-title">Bienvenido</h1>
        <p class="hero-subtitle">Ingresa con tu nombre de usuario y clave.</p>
    </div>

    <form method="post" action="{{ route('login.attempt') }}" class="form-grid">
        @csrf
        <div class="field">
            <label for="login">Usuario o correo</label>
            <input id="login" name="login" class="input" value="{{ old('login') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" class="input" required>
        </div>

        <label style="display:flex; gap:.5rem; align-items:center; color:var(--muted); font-size:.85rem;">
            <input type="checkbox" name="remember" value="1" style="accent-color:#70a2ff;">
            Mantener sesión
        </label>

        <button class="btn btn-primary" type="submit">Ingresar</button>
    </form>

    <div class="auth-row">
        <span class="muted" style="font-size:.88rem;">o continúa con</span>
    </div>

    <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline oauth-btn">
        <span class="oauth-icon">G</span>
        Ingresar con Google
    </a>

    <div class="auth-row">
        <span class="muted" style="font-size:.85rem;">¿No tienes cuenta?</span>
        <a href="{{ route('register') }}" class="auth-link">Regístrate</a>
    </div>
@endsection
