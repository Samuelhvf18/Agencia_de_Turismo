<?php

namespace App\Services\Pagos;

use App\Models\ComprobantePago;
use App\Observers\AuditObserver;
use Barryvdh\DomPDF\Facade\Pdf;

/** Comprobante PDF de pago. */
class ComprobanteService
{
    public function pdf(ComprobantePago $comprobante): \Barryvdh\DomPDF\PDF
    {
        $comprobante->loadMissing('reserva.salida.tour', 'reserva.participantes.alquileres.item.tipo', 'pagos');
        $comprobante->reserva->participantes->each(fn ($p) => AuditObserver::lectura($p));

        return Pdf::loadView('pdf.comprobante', [
            'comprobante' => $comprobante,
            'reserva' => $comprobante->reserva,
            'tour' => $comprobante->reserva->salida->tour,
            'salida' => $comprobante->reserva->salida,
            'metodos' => PagoService::METODOS + ['efectivo' => 'Efectivo'],
        ])->setPaper('a4', 'portrait');
    }
}
