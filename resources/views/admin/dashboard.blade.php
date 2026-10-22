@extends('layouts.admin')

@section('header-title', 'Panel General de Resumen')

@section('content')
<div class="space-y-7">

    {{-- Bienvenida --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1F5F8B] via-[#245F85] to-[#174870] p-6 sm:p-8 text-white shadow-lg">
        <div class="absolute -right-12 -top-20 h-64 w-64 rounded-full border-[35px] border-white/[0.05]"></div>
        <div class="absolute right-20 -bottom-24 h-48 w-48 rounded-full bg-[#457699]/40 blur-2xl"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[.15em] text-[#F4C95D]">
                <i class="fa-solid fa-mountain-sun"></i>
                Centro de operaciones
            </div>

            <h3 class="mt-4 font-display text-3xl sm:text-4xl leading-tight">
                Tu agencia, <span class="italic text-[#F4C95D]">bajo control.</span>
            </h3>

            <p class="mt-3 max-w-xl text-sm leading-6 text-white/75">
                Supervisa la flota, las salidas y el equipamiento de Pipocas desde un solo lugar.
            </p>
        </div>

        <div class="relative z-10 mt-6 flex items-center gap-2 text-xs text-white/65">
            <i class="fa-solid fa-location-dot text-[#F4C95D]"></i>
            <span>La Paz, Bolivia</span>
            <span class="text-white/30">•</span>
            <span>Panel administrativo</span>
        </div>
    </section>

    {{-- Indicadores principales --}}
    <section>
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[.17em] text-[#457699]">
                    Resumen operativo
                </p>
                <h3 class="mt-1 font-display text-xl text-[#343A40]">
                    Estado de la agencia
                </h3>
            </div>
            <span class="hidden sm:inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-[10px] font-bold text-[#6B6F72] border border-[#1F5F8B]/10">
                <i class="fa-solid fa-chart-simple text-[#C96F4A]"></i>
                Indicadores generales
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5">

            {{-- Flota --}}
            <article class="admin-card relative overflow-hidden p-5 sm:p-6">
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-[#1F5F8B]/[0.05]"></div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#6B6F72]">
                            Flota disponible
                        </p>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-[#1F5F8B]">
                                {{ $vehiculosDisponibles ?? 0 }}
                            </span>
                            <span class="text-[10px] font-semibold text-[#6B6F72]">disponibles</span>
                        </div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#1F5F8B]/10 text-[#1F5F8B]">
                        <i class="fa-solid fa-van-shuttle text-lg"></i>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#1F5F8B]/10 pt-3">
                    <p class="text-[10px] text-[#6B6F72]">
                        <span class="font-bold text-[#C96F4A]">{{ $vehiculosMantenimiento ?? 0 }}</span>
                        en mantenimiento
                    </p>
                    <p class="mt-1 text-[10px] text-[#6B6F72]/80">
                        De un total de {{ $totalVehiculos ?? 0 }} unidades
                    </p>
                </div>
            </article>

            {{-- Salidas --}}
            <article class="admin-card relative overflow-hidden p-5 sm:p-6">
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-[#C96F4A]/[0.07]"></div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#6B6F72]">
                            Salidas programadas
                        </p>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-[#C96F4A]">
                                {{ $salidasProgramadas ?? 0 }}
                            </span>
                            <span class="text-[10px] font-semibold text-[#6B6F72]">viajes</span>
                        </div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#C96F4A]/10 text-[#C96F4A]">
                        <i class="fa-solid fa-route text-lg"></i>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#1F5F8B]/10 pt-3">
                    <p class="text-[10px] text-[#6B6F72]">
                        Operaciones activas registradas
                    </p>
                </div>
            </article>

            {{-- Equipos --}}
            <article class="admin-card relative overflow-hidden p-5 sm:p-6">
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-[#F4C95D]/[0.15]"></div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#6B6F72]">
                            Equipos disponibles
                        </p>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-[#1F5F8B]">
                                {{ $equiposDisponibles ?? 0 }}
                            </span>
                            <span class="text-[10px] font-semibold text-[#6B6F72]">
                                / {{ $equiposTotal ?? 0 }} total
                            </span>
                        </div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F4C95D]/25 text-[#6B4F3A]">
                        <i class="fa-solid fa-toolbox text-lg"></i>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#1F5F8B]/10 pt-3">
                    <p class="text-[10px] text-[#6B6F72]">
                        Inventario y accesorios
                    </p>
                </div>
            </article>

            {{-- Ingresos --}}
            <article class="admin-card relative overflow-hidden p-5 sm:p-6">
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-emerald-500/[0.06]"></div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#6B6F72]">
                            Ingresos registrados
                        </p>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-emerald-600">
                                $0.00
                            </span>
                        </div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#1F5F8B]/10 pt-3">
                    <p class="text-[10px] text-[#6B6F72]">
                        Facturación general
                    </p>
                </div>
            </article>

        </div>
    </section>

    {{-- Operaciones y pagos --}}
    <section class="grid grid-cols-1 2xl:grid-cols-3 gap-5">

        {{-- Tabla de salidas --}}
        <div class="admin-card min-w-0 p-5 sm:p-6 2xl:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#1F5F8B]/10 text-[#1F5F8B]">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-[#343A40]">
                                Monitoreo de operaciones
                            </h3>
                            <p class="mt-1 text-[10px] text-[#6B6F72]">
                                Salidas y vehículos registrados
                            </p>
                        </div>
                    </div>
                </div>

                <span class="rounded-full bg-[#F4C95D]/20 px-3 py-1.5 text-[9px] font-extrabold uppercase tracking-wider text-[#6B4F3A]">
                    Últimas salidas
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[450px] text-left text-xs">
                    <thead>
                        <tr>
                            <th class="border-b border-[#1F5F8B]/10 pb-3 pr-4">Tour</th>
                            <th class="border-b border-[#1F5F8B]/10 pb-3 pr-4">Fecha</th>
                            <th class="border-b border-[#1F5F8B]/10 pb-3">Vehículo</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($salidas ?? [] as $salida)
                            <tr>
                                <td class="py-4 pr-4 font-bold text-[#343A40]">
                                    {{ $salida->tour->nom ?? 'Tour General' }}
                                </td>
                                <td class="py-4 pr-4 whitespace-nowrap text-[#6B6F72]">
                                    {{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}
                                </td>
                                <td class="py-4">
                                    <span class="inline-flex items-center gap-2 rounded-lg bg-[#1F5F8B]/[0.07] px-2.5 py-1.5 font-mono font-bold text-[#1F5F8B]">
                                        <i class="fa-solid fa-van-shuttle text-[10px]"></i>
                                        {{ $salida->vehiculo->pla ?? 'Sin asignar' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-10 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1F5F8B]/[0.07] text-[#457699]">
                                        <i class="fa-regular fa-calendar-xmark text-lg"></i>
                                    </div>
                                    <p class="mt-3 text-xs font-bold text-[#343A40]">
                                        No hay salidas registradas
                                    </p>
                                    <p class="mt-1 text-[10px] text-[#6B6F72]">
                                        Las operaciones aparecerán aquí cuando existan registros.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagos --}}
        <div class="admin-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#C96F4A]/10 text-[#C96F4A]">
                    <i class="fa-solid fa-receipt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-[#343A40]">
                        Últimos pagos
                    </h3>
                    <p class="mt-1 text-[10px] text-[#6B6F72]">
                        Resumen de movimientos
                    </p>
                </div>
            </div>

            <div class="flex min-h-[190px] flex-col items-center justify-center rounded-2xl border border-dashed border-[#1F5F8B]/15 bg-[#F0F0F1]/60 px-4 py-8 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#C96F4A] shadow-sm">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <p class="mt-4 text-xs font-bold text-[#343A40]">
                    Sin pagos registrados
                </p>
                <p class="mt-2 max-w-[220px] text-[10px] leading-5 text-[#6B6F72]">
                    Cuando haya pagos registrados, podrás consultar aquí su resumen.
                </p>
            </div>
        </div>

    </section>
</div>
@endsection