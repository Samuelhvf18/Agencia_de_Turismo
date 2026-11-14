<?php

namespace App\Mail;

use App\Models\RegistroNotificacion;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Correo genérico para los avisos de registros_notificacion cuando no se usa n8n. */
class AvisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RegistroNotificacion $registro) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __(NotificacionService::TIPOS[$this->registro->tip] ?? 'Aviso').' · Pipocas');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.avisos.generico', with: [
            'registro' => $this->registro,
            'reserva' => $this->registro->reserva,
            'datos' => $this->registro->cnd ?? [],
            'titulo' => __(NotificacionService::TIPOS[$this->registro->tip] ?? 'Aviso'),
        ]);
    }
}
