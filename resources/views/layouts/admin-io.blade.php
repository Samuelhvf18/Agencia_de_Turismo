@extends('layouts.admin')
@section('header-title')
    @yield('titulo', 'Planificación inteligente')
@endsection
@section('page-description')
    @yield('subtitulo', 'Convierte los datos de tu agencia en decisiones para el día a día.')
@endsection
@section('page-actions')
    @yield('acciones')
@endsection
@section('content')
    @php
        $preview = request()->is('admin/io/preview*');
        $pestanas = [
            [route('admin.io.index'), 'Para empezar', 'admin.io.index', 'fa-compass'],
            [
                $preview ? url('admin/io/preview/colas') : route('admin.io.colas'),
                'Demanda y capacidad',
                'admin.io.colas,admin.io.preview.colas',
                'fa-people-line',
            ],
            [route('admin.io.propuestas'), 'Asignar recursos', 'admin.io.propuestas*', 'fa-route'],
            [route('admin.io.eoq'), 'Planificar equipo', 'admin.io.eoq*', 'fa-boxes-stacked'],
            [
                $preview ? url('admin/io/preview/ciclo-vida') : route('admin.io.ciclo-vida'),
                'Retener clientes',
                'admin.io.ciclo-vida,admin.io.preview.ciclo-vida',
                'fa-heart',
            ],
            [route('admin.io.parametros'), 'Ajustes del análisis', 'admin.io.parametros*', 'fa-sliders'],
        ];
    @endphp
    <nav class="admin-tabs" aria-label="Herramientas de planificación">
        @foreach ($pestanas as [$url, $texto, $patrones, $icono])
            <a href="{{ $url }}" class="{{ request()->routeIs(...explode(',', $patrones)) ? 'is-active' : '' }}"
                @if (request()->routeIs(...explode(',', $patrones))) aria-current="page" @endif><i aria-hidden="true" class="fa-solid {{ $icono }}"></i>{{ $texto }}</a>
        @endforeach
    </nav>
    <div class="io-content space-y-6">
        @if (!empty($demo))
            <div class="admin-notice admin-notice-amber" role="status"><i aria-hidden="true" class="fa-solid fa-flask"></i>
                <div><strong>Estás viendo un ejemplo, no datos operativos.</strong>
                    <p>Usa esta pantalla para conocer la herramienta. Las sugerencias de esta demostración no deben
                        aplicarse a salidas reales.</p>
                </div>
            </div>
        @endif
        @if (session('estado'))
            <div class="admin-notice admin-notice-success" role="status"><i aria-hidden="true" class="fa-solid fa-circle-check"></i>
                <p>{{ session('estado') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="admin-notice admin-notice-danger" role="alert"><i aria-hidden="true" class="fa-solid fa-circle-exclamation"></i>
                <div><strong>Revisa los datos antes de continuar</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @yield('contenido')
    </div>
@endsection
