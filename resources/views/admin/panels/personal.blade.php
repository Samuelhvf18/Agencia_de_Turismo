@extends('layouts.admin')

@section('header-title', 'Gestión de Personal y Guías')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Equipo Operativo</h3>
            <p class="text-xs text-[#6B6F72]">Guías turísticos autorizados, choferes y personal de montaña.</p>
        </div>
        <a href="{{ route('admin.seguridad') }}" class="px-4 py-2 bg-[#1F5F8B] hover:bg-[#184B6E] text-white rounded-xl text-sm font-semibold transition">
            Gestionar Usuarios
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-[#6B6F72]">Guías Activos</span>
                    <h4 class="text-2xl font-bold text-[#1F5F8B] mt-1">
                        {{ \App\Models\User::whereHas('rol', fn($r) => $r->where('nom', 'guia'))->count() }}
                    </h4>
                </div>
                <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#1F5F8B] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-person-hiking"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-[#6B6F72]">Choferes Asignados</span>
                    <h4 class="text-2xl font-bold text-[#C96F4A] mt-1">
                        {{ \App\Models\User::whereHas('rol', fn($r) => $r->where('nom', 'chofer'))->count() }}
                    </h4>
                </div>
                <div class="h-10 w-10 rounded-xl bg-orange-50 text-[#C96F4A] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-id-card"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-[#6B6F72]">Operaciones y Logística</span>
                    <h4 class="text-2xl font-bold text-[#947412] mt-1">
                        {{ \App\Models\User::whereHas('rol', fn($r) => $r->where('nom', 'operaciones'))->count() }}
                    </h4>
                </div>
                <div class="h-10 w-10 rounded-xl bg-amber-50 text-[#947412] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
