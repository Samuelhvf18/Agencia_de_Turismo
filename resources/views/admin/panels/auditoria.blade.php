@extends('layouts.admin')

@section('header-title', 'Usuarios, Roles y Auditoría de Seguridad')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-base font-bold text-[#343A40] mb-4">Bitácora de Auditoría e IPs Recientes</h3>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-xs font-semibold border-b border-gray-200 uppercase text-[#6B6F72]">
                    <th class="pb-3">Usuario</th>
                    <th class="pb-3">Acción</th>
                    <th class="pb-3">Dirección IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                <tr>
                    <td class="py-3 font-medium text-[#343A40]">Samuel (Admin)</td>
                    <td class="py-3 text-[#6B6F72]">Actualización de tarifas en Tour Salar</td>
                    <td class="py-3 font-mono font-semibold text-[#1F5F8B]">192.168.1.45</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection