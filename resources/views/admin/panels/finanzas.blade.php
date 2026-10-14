@extends('layouts.admin')

@section('header-title', 'Finanzas, Facturación y Pagos')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-[#6B6F72]">Cobros QR (Hoy)</span>
            <h4 class="text-2xl font-bold text-[#1F5F8B] mt-1">$ 2,150.00</h4>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-[#6B6F72]">Tarjetas</span>
            <h4 class="text-2xl font-bold text-[#C96F4A] mt-1">$ 1,800.00</h4>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-[#6B6F72]">Pendientes</span>
            <h4 class="text-2xl font-bold text-[#947412] mt-1">$ 900.00</h4>
        </div>
    </div>
</div>
@endsection