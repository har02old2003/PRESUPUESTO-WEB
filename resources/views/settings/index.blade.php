@extends('layouts.app')

@section('title', 'Ajustes - HAXX COORP')
@section('page_title', 'Ajustes de cuenta')
@section('page_subtitle', 'Gestiona tu perfil, respaldo y salida segura de sesión')

@section('content')
    <section class="grid-2">
        <article class="glass-card" style="padding:1rem;">
            <h2 style="margin:0 0 .6rem; font-size:1.05rem;">Tu cuenta</h2>
            <p style="margin:0;"><strong>Usuario:</strong> {{ $user->username }}</p>
            <p style="margin:.3rem 0 0;"><strong>Nombre:</strong> {{ $user->display_name }}</p>
            <p style="margin:.3rem 0 0;"><strong>Correo:</strong> {{ $user->email }}</p>
            <p style="margin:.3rem 0 0;" class="muted"><strong>Proveedor:</strong> {{ $user->auth_provider }}</p>

            <form action="{{ route('logout') }}" method="post" style="margin-top:.8rem;">
                @csrf
                <button class="btn btn-outline" type="submit">Cerrar sesión</button>
            </form>
        </article>

        <article class="glass-card" style="padding:1rem;">
            <h2 style="margin:0 0 .6rem; font-size:1.05rem;">Datos y respaldo</h2>
            <p class="muted" style="margin:0 0 .8rem;">
                Exporta tus datos en PDF o CSV para respaldo local o auditoría externa.
            </p>

            <div style="display:grid; gap:.55rem;">
                <a href="{{ route('exports.pdf') }}" class="btn btn-primary" style="text-align:center;">Descargar reporte PDF</a>
                <a href="{{ route('exports.csv') }}" class="btn btn-outline" style="text-align:center;">Descargar reporte CSV</a>
            </div>

            <hr style="border-color:rgba(117,155,255,.2); margin:.9rem 0;">
            <h3 style="margin:0 0 .45rem; font-size:.95rem;">Notificaciones</h3>
            <p class="muted" style="margin:0 0 .55rem;">Recibe alertas si superas tu limite semanal o mensual.</p>
            <button class="btn btn-outline" type="button" data-enable-notifications>Activar notificaciones</button>
            <p class="muted notif-status" data-notification-status style="margin:.45rem 0 0;"></p>
        </article>
    </section>
@endsection
