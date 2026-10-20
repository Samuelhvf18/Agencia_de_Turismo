@extends('layouts.admin')

@section('header-title', 'Analítica, Demanda y BI')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-base font-bold text-[#343A40] mb-2">Demanda Proyectada vs Real</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300 text-xs text-[#6B6F72]">
            [Grafico Chart.js]
        </div>
    </div>
    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-base font-bold text-[#343A40] mb-2">Turistas por Nacionalidad</h3>
        <p class="text-xs text-[#6B6F72] mb-4">Francia 35%, USA 25%, Bolivia 20%</p>
    </div>
</div>
@endsection
