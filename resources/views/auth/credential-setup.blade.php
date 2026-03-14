@extends('layouts.guest')

@section('title', 'Completar acceso - HAXX COORP')

@section('content')
    <div>
        <h1 class="hero-title">Completa tu acceso</h1>
        <p class="hero-subtitle">Define usuario y contraseña para completar tu acceso al sistema.</p>
    </div>

    <form method="post" action="{{ route('credential.store') }}" class="form-grid">
        @csrf

        <div class="field">
            <label for="display_name">Cómo quieres ser llamado</label>
            <input id="display_name" name="display_name" class="input" value="{{ old('display_name', $user->display_name) }}" required>
        </div>

        <div class="field">
            <label for="username">Nombre de usuario</label>
            <input id="username" name="username" class="input" value="{{ old('username', $user->username) }}" required>
        </div>

        <div class="field">
            <label for="password">Nueva contraseña</label>
            <input id="password" name="password" type="password" class="input" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>
        </div>

        <button class="btn btn-primary" type="submit">Guardar y continuar</button>
    </form>
@endsection
