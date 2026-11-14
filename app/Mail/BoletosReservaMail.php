<?php

namespace App\Mail;

use App\Models\Reserva;
use App\Services\Pagos\ComprobanteService;
use App\Services\Reservas\BoletoService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Confirmación de la reserva con los boletos QR y el comprobante en PDF. */
class BoletosReservaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Tu reserva :codigo está confirmada', ['codigo' => $this->reserva->cod]).' · Pipocas');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservas.boletos', with: [
            'reserva' => $this->reserva,
            'tour' => $this->reserva->salida->tour,
            'salida' => $this->reserva->salida,
        ]);
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $boletos = $this->reserva->boletos()->where('est', '<>', 'cancelado')->get();
        $adjuntos = [];

        if ($boletos->isNotEmpty()) {
            $pdf = app(BoletoService::class)->pdf($this->reserva, $boletos)->output();
            $adjuntos[] = Attachment::fromData(fn () => $pdf, "boletos-{$this->reserva->cod}.pdf")->withMime('application/pdf');
        }

        if ($this->reserva->comprobante) {
            $pdf = app(ComprobanteService::class)->pdf($this->reserva->comprobante)->output();
            $adjuntos[] = Attachment::fromData(fn () => $pdf, "comprobante-{$this->reserva->comprobante->cod}.pdf")->withMime('application/pdf');
        }

        return $adjuntos;
    }
}
