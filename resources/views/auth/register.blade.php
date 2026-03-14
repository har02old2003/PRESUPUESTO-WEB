@extends('layouts.guest')

@section('title', 'Registro - HAXX COORP')

@section('content')
    <div>
        <h1 class="hero-title">Crear cuenta</h1>
        <p class="hero-subtitle">Tu usuario será tu acceso principal. El correo se usa para verificación.</p>
    </div>

    <form method="post" action="{{ route('register.store') }}" class="form-grid">
        @csrf

        <div class="field">
            <label for="display_name">Nombre visible</label>
            <input id="display_name" name="display_name" class="input" value="{{ old('display_name') }}" required>
        </div>

        <div class="field">
            <label for="username">Nombre de usuario</label>
            <input id="username" name="username" class="input" value="{{ old('username') }}" placeholder="haxx_user" required>
        </div>

        <div class="field">
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" class="input" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>
        </div>

        <button class="btn btn-primary" type="submit">Crear cuenta</button>
    </form>

    <div class="auth-row">
        <span class="muted" style="font-size:.85rem;">¿Ya tienes cuenta?</span>
        <a href="{{ route('login') }}" class="auth-link">Inicia sesión</a>
    </div>
@endsection
