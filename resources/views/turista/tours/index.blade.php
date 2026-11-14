@extends('layouts.turista')
@section('title', 'Explorar tours')
@section('topbar-title', 'Explorar tours')
@section('content')
    <div class="mb-7">
        <p class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-pipocas-terra">
            Descubre Bolivia
        </p>
        <h1 class="page-title text-3xl font-semibold sm:text-4xl">
            Encuentra tu próxima aventura
        </h1>
        <p class="mt-2 text-sm text-pipocas-muted">
            Explora experiencias disponibles y conoce todos sus detalles.
        </p>
    </div>
    <form method="GET" class="tourist-card mb-6 grid gap-3 p-4 sm:grid-cols-[1fr_220px_auto]">
        <input
            class="field"
            type="search"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por nombre o descripción...">
        <select class="field" name="categoria">
            <option value="">Todas las categorías</option>

            @foreach($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    @selected(request('categoria') == $categoria->id)>
                    {{ $categoria->nom }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-magnifying-glass mr-2"></i>
            Buscar
        </button>
    </form>
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($tours as $tour)
            <article class="tourist-card overflow-hidden">
                <div class="relative grid h-48 place-items-center overflow-hidden bg-gradient-to-br from-[#1F5F8B] via-[#457699] to-[#174870]">
                    <div class="absolute -right-8 -top-12 h-40 w-40 rounded-full bg-white/5"></div>
                    <div class="absolute -bottom-12 -left-8 h-40 w-40 rounded-full bg-[#F4C95D]/10"></div>
                    <div class="relative z-10 text-center">
                        <i class="fa-solid fa-mountain-sun text-6xl text-[#F4C95D]"></i>
                        <p class="mt-3 text-xs font-bold uppercase tracking-[.2em] text-white/80">
                            Descubre Bolivia
                        </p>
                    </div>
                    <div class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-[10px] font-bold text-pipocas-blue">
                        {{ $tour->categoria->nom ?? 'Tour' }}
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-pipocas-muted">
                            {{ ucfirst($tour->niv_dif) }}
                        </span>
                        <span class="text-xs text-pipocas-muted">
                            <i class="fa-regular fa-clock mr-1"></i>
                            {{ $tour->dur_hrs }} h
                        </span>
                    </div>
                    <h2 class="mt-2 text-lg font-extrabold">
                        {{ $tour->nom }}
                    </h2>
                    <p class="mt-2 min-h-[42px] text-xs leading-5 text-pipocas-muted">
                        {{ \Illuminate\Support\Str::limit($tour->des, 100) }}
                    </p>
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <div>
                            <p class="text-[10px] text-pipocas-muted">
                                Desde
                            </p>
                            <p class="text-lg font-extrabold text-pipocas-blue">
                                Bs {{ number_format((float) $tour->pre_bas, 2) }}
                            </p>
                        </div>
                        <a href="{{ route('turista.tours.show', $tour) }}"
                            class="btn-primary">
                            Ver tour
                            <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="tourist-card p-10 text-center sm:col-span-2 xl:col-span-3">
                <i class="fa-solid fa-binoculars mb-3 text-3xl text-pipocas-blue/40"></i>
                <p class="font-bold">
                    No encontramos tours con esos filtros.
                </p>
                <a href="{{ route('turista.tours.index') }}"
                    class="mt-3 inline-block text-xs font-bold text-pipocas-blue">
                    Limpiar búsqueda
                </a>
            </div>
        @endforelse
    </div>
    <div class="mt-7">
        {{ $tours->links() }}
    </div>
@endsection