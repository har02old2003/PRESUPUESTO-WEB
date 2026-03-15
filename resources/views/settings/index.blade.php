@extends('layouts.app')

@section('title', 'Ajustes - HAXX COORP')
@section('page_title', 'Ajustes de cuenta')
@section('page_subtitle', 'Gestiona tu perfil, respaldo y salida segura de sesión')

@section('content')
    <section class="grid-2">
        <article class="glass-card section-block">
            <h2 class="section-title">Tu cuenta</h2>
            <div class="soft-list" style="margin-top:.7rem;">
                <article class="soft-item">
                    <div class="soft-item-body">
                        <p class="soft-item-title">Usuario</p>
                        <p class="soft-item-sub">{{ $user->username }}</p>
                    </div>
                </article>
                <article class="soft-item">
                    <div class="soft-item-body">
                        <p class="soft-item-title">Nombre</p>
                        <p class="soft-item-sub">{{ $user->display_name }}</p>
                    </div>
                </article>
                <article class="soft-item">
                    <div class="soft-item-body">
                        <p class="soft-item-title">Correo</p>
                        <p class="soft-item-sub">{{ $user->email }}</p>
                    </div>
                </article>
                <article class="soft-item">
                    <div class="soft-item-body">
                        <p class="soft-item-title">Proveedor</p>
                        <p class="soft-item-sub">{{ $user->auth_provider }}</p>
                    </div>
                </article>
            </div>

            <form action="{{ route('logout') }}" method="post" style="margin-top:.8rem;">
                @csrf
                <button class="btn btn-outline" type="submit">Cerrar sesión</button>
            </form>
        </article>

        <article class="glass-card section-block">
            <h2 class="section-title">Datos y respaldo</h2>
            <p class="section-note" style="margin:0 0 .8rem;">
                Exporta tus datos en PDF o CSV para respaldo local o auditoría externa.
            </p>

            <div style="display:grid; gap:.55rem;">
                <a href="{{ route('exports.pdf') }}" class="btn btn-primary" style="text-align:center;">Descargar reporte PDF</a>
                <a href="{{ route('exports.csv') }}" class="btn btn-outline" style="text-align:center;">Descargar reporte CSV</a>
            </div>

            <hr style="border-color:rgba(117,155,255,.2); margin:.9rem 0;">
            <h3 class="section-title" style="font-size:1rem;">Notificaciones</h3>
            <p class="section-note" style="margin:0 0 .55rem;">Recibe alertas si superas tu límite semanal o mensual.</p>
            <button class="btn btn-outline" type="button" data-enable-notifications>Activar notificaciones</button>
            <p class="muted notif-status" data-notification-status style="margin:.45rem 0 0;"></p>
        </article>
    </section>
@endsection
