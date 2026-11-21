<?php

namespace App\Services\Reservas;

use App\Models\Boleto;
use App\Models\Reserva;
use App\Observers\AuditObserver;
use App\Services\CodeGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Collection;

/** Un boleto QR por participante. */
class BoletoService
{
    /**
     * Exige ficha en alto riesgo.
     *
     * @return Collection<int, Boleto> boletos emitidos ahora
     */
    public function emitir(Reserva $reserva): Collection
    {
        $reserva->loadMissing('salida.tour', 'participantes.boleto', 'participantes.fichaMedica');
        $altoRiesgo = $reserva->salida->tour->esAltoRiesgo();
        $emitidos = collect();

        foreach ($reserva->participantes as $ptc) {
            if (app(SeguridadParticipantes::class)->bloqueo($ptc, $reserva->salida)) {
                continue;
            }
            if ($altoRiesgo && (! $ptc->fichaMedica?->fir_en || ! FirmaDigital::existe($ptc->fichaMedica?->rta_fir))) {
                continue;
            }
            if ($ptc->boleto) {
                if ($ptc->boleto->est === Boleto::EST_CANCELADO) {
                    $ptc->boleto->update(['est' => Boleto::EST_EMITIDO]);
                }

                continue;
            }

            $emitidos->push(Boleto::create([
                'cod' => CodeGeneratorService::generate('BOL'),
                'ptc_rsv_id' => $ptc->id,
                'est' => Boleto::EST_EMITIDO,
            ]));
        }

        return $emitidos;
    }

    /** QR SVG sin extensión GD. */
    public function qrDataUri(string $codigo): string
    {
        $opciones = new QROptions([
            'outputBase64' => true,
            'eccLevel' => EccLevel::M,
            'scale' => 5,
            'addQuietzone' => true,
        ]);

        return (new QRCode($opciones))->render($codigo);
    }

    /** Un boleto por página. */
    public function pdf(Reserva $reserva, Collection $boletos): \Barryvdh\DomPDF\PDF
    {
        $reserva->loadMissing('salida.tour.categoria', 'salida.guia.usuario');
        // Acepta también colecciones Support.
        $boletos->each(fn (Boleto $boleto) => $boleto->loadMissing('participante.pais'));
        $boletos->each(fn ($boleto) => AuditObserver::lectura($boleto->participante));

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
