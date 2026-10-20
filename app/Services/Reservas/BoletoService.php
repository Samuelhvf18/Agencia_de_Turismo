<?php

namespace App\Services\Reservas;

use App\Models\Boleto;
use App\Models\Reserva;
use App\Services\CodeGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Collection;

/**
 * Boletos con código QR por participante (RF-10, RF-11). El QR contiene el
 * código del boleto; el guía lo escanea en el check-in.
 */
class BoletoService
{
    /**
     * Emite el boleto de cada participante que aún no lo tiene. En tours de
     * alto riesgo, quien no tiene ficha médica firmada queda sin boleto.
     *
     * @return Collection<int, Boleto> boletos emitidos ahora
     */
    public function emitir(Reserva $reserva): Collection
    {
        $reserva->loadMissing('salida.tour', 'participantes.boleto', 'participantes.fichaMedica');
        $altoRiesgo = $reserva->salida->tour->esAltoRiesgo();
        $emitidos = collect();

        foreach ($reserva->participantes as $ptc) {
            if ($ptc->boleto) {
                if ($ptc->boleto->est === Boleto::EST_CANCELADO) {
                    $ptc->boleto->update(['est' => Boleto::EST_EMITIDO]);
                }
                continue;
            }
            if ($altoRiesgo && ! $ptc->fichaMedica) {
                continue; // bloqueado hasta que firme la ficha médica
            }

            $emitidos->push(Boleto::create([
                'cod' => CodeGeneratorService::generate('BOL'),
                'ptc_rsv_id' => $ptc->id,
                'est' => Boleto::EST_EMITIDO,
            ]));
        }

        return $emitidos;
    }

    /** QR del boleto como data URI SVG (no requiere la extensión GD). */
    public function qrDataUri(string $codigo): string
    {
        $opciones = new QROptions([
            'outputBase64' => true,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::M,
            'scale' => 5,
            'addQuietzone' => true,
        ]);

        return (new QRCode($opciones))->render($codigo);
    }

    /** PDF con los boletos indicados (uno por página). */
    public function pdf(Reserva $reserva, Collection $boletos): \Barryvdh\DomPDF\PDF
    {
        $reserva->loadMissing('salida.tour.categoria', 'salida.guia.usuario');
        $boletos->loadMissing('participante.pais');

        $items = $boletos->map(fn (Boleto $b) => [
            'boleto' => $b,
            'qr' => $this->qrDataUri($b->cod),
        ]);

        return Pdf::loadView('pdf.boletos', [
            'reserva' => $reserva,
            'salida' => $reserva->salida,
            'tour' => $reserva->salida->tour,
            'items' => $items,
        ])->setPaper('a5', 'portrait');
    }
}
