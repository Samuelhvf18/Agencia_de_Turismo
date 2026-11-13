<div class="space-y-6">

    @if($mensajeExito)
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between transition-all">
            <span><i class="fa-solid fa-circle-check mr-2"></i> {{ $mensajeExito }}</span>
            <button wire:click="$set('mensajeExito', '')" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Cobros de Hoy</span>
                <h4 class="text-2xl font-bold text-[#1F5F8B] mt-1">Bs. {{ number_format($cobrosHoy, 2) }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#1F5F8B] flex items-center justify-center font-bold">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Recaudación Total</span>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">Bs. {{ number_format($cobrosTotal, 2) }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Comprobantes Pendientes</span>
                <h4 class="text-2xl font-bold text-[#C96F4A] mt-1">{{ $comprobantesPendientes }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-orange-50 text-[#C96F4A] flex items-center justify-center font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Por Cobrar</span>
                <h4 class="text-2xl font-bold text-[#947412] mt-1">Bs. {{ number_format($montoPendiente, 2) }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-amber-50 text-[#947412] flex items-center justify-center font-bold">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2 flex-1">
            <div class="w-full sm:w-80 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar factura, reserva o contacto..." 
                       class="w-full bg-gray-50 border border-gray-300 rounded-xl pl-9 pr-4 py-2 text-sm text-[#343A40] focus:ring-[#1F5F8B] focus:border-[#1F5F8B]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                <span wire:loading wire:target="search" class="absolute right-3 top-2.5 text-xs text-[#1F5F8B]">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </span>
            </div>
            
            <select wire:model.live="estado" class="bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm text-[#343A40]">
                <option value="">-- Todos los Estados --</option>
                <option value="emitido">Emitido (Pendiente)</option>
                <option value="pagado">Pagado</option>
                <option value="anulado">Desactivado / Anulado</option>
            </select>

            @if(!empty($search) || !empty($estado))
                <button wire:click="$set('search', ''); $set('estado', '')" 
                        class="px-3 py-2 border rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-50">
                    Limpiar
                </button>
            @endif
        </div>

        <a href="{{ route('admin.finanzas.exportar') }}" 
           onclick="Swal.fire('Descargando...', 'Tu reporte en Excel se está descargando', 'success')"
           class="w-full sm:w-auto px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-file-excel text-sm"></i>
            <span>Exportar a Excel</span>
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                    <tr>
                        <th class="p-4">Comprobante / Fecha</th>
                        <th class="p-4">Reserva Vinculada</th>
                        <th class="p-4">Monto Total</th>
                        <th class="p-4">Pagos Realizados</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($comprobantes as $comp)
                    @php
                        $pagado = $comp->pagos->where('est', 'completado')->sum('mto');
                        $pendiente = max(0, $comp->mto_tot - $pagado);
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors {{ $comp->est === 'anulado' ? 'opacity-60 bg-gray-50/50' : '' }}">
                        <td class="p-4">
                            <span class="font-bold text-[#1F5F8B] text-sm font-mono">#{{ $comp->cod }}</span>
                            <div class="text-[#6B6F72] mt-0.5">{{ \Carbon\Carbon::parse($comp->fec_emi)->format('d/m/Y') }}</div>
                        </td>
                        <td class="p-4 text-[#343A40]">
                            @if($comp->reserva)
                                <div class="font-semibold">{{ $comp->reserva->nom_ctc }}</div>
                                <div class="text-[11px] text-[#6B6F72]">Reserva: <span class="font-mono font-bold">{{ $comp->reserva->cod }}</span></div>
                                <div class="text-[10px] text-gray-400">{{ $comp->reserva->salidaTour->tour->nom ?? 'Tour general' }}</div>
                            @else
                                <span class="text-gray-400 italic">Sin reserva asociada</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-sm text-[#343A40]">Bs. {{ number_format($comp->mto_tot, 2) }}</div>
                            @if($pendiente > 0 && $comp->est !== 'anulado')
                                <span class="text-[10px] text-amber-600 font-semibold">Resta: Bs. {{ number_format($pendiente, 2) }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($comp->pagos->count() > 0)
                                <span class="font-semibold text-emerald-700">Bs. {{ number_format($pagado, 2) }}</span>
                                <div class="text-[10px] text-gray-500">({{ $comp->pagos->count() }} pago(s))</div>
                            @else
                                <span class="text-gray-400 italic">Sin cobros aún</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($comp->est === 'pagado')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Pagado
                                </span>
                            @elseif($comp->est === 'emitido')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-clock mr-1"></i> Pendiente
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-red-100 text-red-800 border border-red-200">
                                    <i class="fa-solid fa-ban mr-1"></i> Desactivado
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            @if($comp->est !== 'anulado' && $comp->est !== 'pagado')
                                <button type="button" 
                                        wire:click="abrirModalCobro({{ $comp->id }})" 
                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                    + Cobrar
                                </button>
                            @endif

                            <button type="button" 
                                     wire:click="toggleEstado({{ $comp->id }})" 
                                     wire:loading.attr="disabled"
                                     class="text-xs font-bold {{ $comp->est === 'anulado' ? 'text-blue-600 hover:underline' : 'text-red-500 hover:underline' }}">
                                 {{ $comp->est === 'anulado' ? 'Reactivar' : 'Desactivar' }}
                             </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-[#6B6F72]">No se encontraron comprobantes coincidentes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $comprobantes->links() }}
        </div>
    </div>

    @if($modalPagoAbierto)
    <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-base font-bold text-gray-800">Registrar Cobro</h3>
                <button type="button" wire:click="cerrarModalCobro" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
            </div>

            <form wire:submit.prevent="registrarPago" class="space-y-3">
                <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-100 text-xs text-blue-900">
                    <p>Comprobante: <strong>#{{ $comprobanteCod }}</strong></p>
                    <p>Monto restante por cobrar: <strong class="text-emerald-700">Bs. {{ number_format($montoRestante, 2) }}</strong></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Método de Pago *</label>
                    <select wire:model="metodoPago" class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        <option value="QR">Cobro QR (Simple)</option>
                        <option value="Efectivo">Efectivo (Ventanilla)</option>
                    </select>
                    @error('metodoPago') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Monto a Cobrar (Bs.) *</label>
                    <input type="number" step="0.01" min="1" max="{{ $montoRestante }}" wire:model="montoCobro" 
                           class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    @error('montoCobro') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nº Referencia / Recibo (Opcional)</label>
                    <input type="text" wire:model="referencia" placeholder="Ej. TRX-12345 o Nº de comprobante" 
                           class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    @error('referencia') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button type="button" wire:click="cerrarModalCobro" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition flex items-center gap-1">
                        <span wire:loading.remove>Guardar Cobro</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
