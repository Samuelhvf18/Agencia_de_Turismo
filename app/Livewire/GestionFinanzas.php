<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ComprobantePago;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class GestionFinanzas extends Component
{
    use WithPagination;

    public $search = '';
    public $estado = '';

    public $modalPagoAbierto = false;
    public $comprobanteId = null;
    public $comprobanteCod = '';
    public $montoRestante = 0;
    public $montoCobro = 0;
    public $metodoPago = 'QR';
    public $referencia = '';

    public $mensajeExito = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function abrirModalCobro($id)
    {
        $this->resetValidation();
        $comp = ComprobantePago::with('pagos')->findOrFail($id);

        $pagado = $comp->pagos->where('est', 'completado')->sum('mto');
        $restante = max(0, $comp->mto_tot - $pagado);

        $this->comprobanteId = $comp->id;
        $this->comprobanteCod = $comp->cod;
        $this->montoRestante = $restante;
        $this->montoCobro = $restante;
        $this->metodoPago = 'QR';
        $this->referencia = '';

        $this->modalPagoAbierto = true;
    }

    public function cerrarModalCobro()
    {
        $this->modalPagoAbierto = false;
        $this->resetValidation();
    }

    public function registrarPago()
    {
        $this->validate([
            'metodoPago' => 'required|in:QR,Efectivo',
            'montoCobro' => 'required|numeric|min:1|max:' . $this->montoRestante,
            'referencia' => 'nullable|string|max:100',
        ], [
            'metodoPago.required' => 'Seleccione un método de pago.',
            'montoCobro.required' => 'Ingrese el monto a cobrar.',
            'montoCobro.min'      => 'El monto debe ser de al menos Bs. 1.',
            'montoCobro.max'      => 'El monto a cobrar no puede superar el saldo restante.',
        ]);

        $comprobante = ComprobantePago::with('reserva')->findOrFail($this->comprobanteId);

        DB::transaction(function () use ($comprobante) {
            Pago::create([
                'cmp_pag_id' => $comprobante->id,
                'mtd'        => $this->metodoPago,
                'mto'        => $this->montoCobro,
                'ref_trx'    => $this->referencia ? trim($this->referencia) : 'TRX-' . strtoupper(uniqid()),
                'est'        => 'completado',
            ]);

            $totalPagado = Pago::where('cmp_pag_id', $comprobante->id)
                ->where('est', 'completado')
                ->sum('mto');

            if ($totalPagado >= $comprobante->mto_tot) {
                $comprobante->update(['est' => 'pagado']);
                if ($comprobante->reserva) {
                    $comprobante->reserva->update(['est_pag' => 'completado']);
                }
            } else {
                if ($comprobante->reserva) {
                    $comprobante->reserva->update(['est_pag' => 'parcial']);
                }
            }
        });

        $this->mensajeExito = "¡Cobro de Bs. {$this->montoCobro} registrado con éxito en #{$comprobante->cod}!";
        $this->dispatch('swal:alert', [
            'icon'  => 'success',
            'title' => '¡Cobro Registrado!',
            'text'  => "Se registró el cobro de Bs. {$this->montoCobro} en el comprobante #{$comprobante->cod}."
        ]);
        $this->cerrarModalCobro();
    }

    public function toggleEstado($id)
    {
        $comp = ComprobantePago::findOrFail($id);

        if ($comp->est !== 'anulado') {
            $comp->update(['est' => 'anulado']);
            if ($comp->reserva) {
                $comp->reserva->update(['est_pag' => 'cancelado']);
            }
            $this->mensajeExito = "Comprobante #{$comp->cod} desactivado/anulado.";
            $this->dispatch('swal:toast', [
                'icon'  => 'warning',
                'title' => "Comprobante #{$comp->cod} desactivado"
            ]);
        } else {
            $comp->update(['est' => 'emitido']);
            if ($comp->reserva) {
                $comp->reserva->update(['est_pag' => 'pendiente']);
            }
            $this->mensajeExito = "Comprobante #{$comp->cod} reactivado con éxito.";
            $this->dispatch('swal:toast', [
                'icon'  => 'success',
                'title' => "Comprobante #{$comp->cod} reactivado"
            ]);
        }
    }

    public function render()
    {
        $query = ComprobantePago::with(['reserva.salidaTour.tour', 'pagos']);

        if (!empty($this->search)) {
            $buscar = trim($this->search);
            $query->where(function ($q) use ($buscar) {
                $q->where('cod', 'ILIKE', "%{$buscar}%")
                  ->orWhereHas('reserva', function ($r) use ($buscar) {
                      $r->where('cod', 'ILIKE', "%{$buscar}%")
                        ->orWhere('nom_ctc', 'ILIKE', "%{$buscar}%");
                  });
            });
        }

        if (!empty($this->estado)) {
            $query->where('est', $this->estado);
        }

        $comprobantes = $query->orderBy('id', 'desc')->paginate(10);

        $cobrosHoy = Pago::whereDate('pag_en', now()->toDateString())->where('est', 'completado')->sum('mto');
        $cobrosTotal = Pago::where('est', 'completado')->sum('mto');
        $comprobantesPendientes = ComprobantePago::where('est', 'emitido')->count();
        $montoPendiente = ComprobantePago::where('est', 'emitido')->sum('mto_tot');

        return view('livewire.gestion-finanzas', compact(
            'comprobantes',
            'cobrosHoy',
            'cobrosTotal',
            'comprobantesPendientes',
            'montoPendiente'
        ));
    }
}
