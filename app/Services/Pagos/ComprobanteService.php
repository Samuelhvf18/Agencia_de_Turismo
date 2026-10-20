<?php

namespace App\Services\Pagos;

use App\Models\ComprobantePago;
use Barryvdh\DomPDF\Facade\Pdf;

/** Comprobante de pago en PDF (RF-14). */
class ComprobanteService
{
    public function pdf(ComprobantePago $comprobante): \Barryvdh\DomPDF\PDF
    {
        $comprobante->loadMissing('reserva.salida.tour', 'reserva.participantes.alquileres.item.tipo', 'pagos');

        return Pdf::loadView('pdf.comprobante', [
            'comprobante' => $comprobante,
            'reserva' => $comprobante->reserva,
            'tour' => $comprobante->reserva->salida->tour,
            'salida' => $comprobante->reserva->salida,
            'metodos' => PagoService::METODOS + ['efectivo' => 'Efectivo'],
        ])->setPaper('a4', 'portrait');
    }
}
