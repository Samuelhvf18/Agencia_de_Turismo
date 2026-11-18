<?php

namespace App\Livewire;

use App\Models\ConversacionChatbot;
use App\Services\Chatbot\ChatbotService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class AsistenteTuristico extends Component
{
    public string $mensaje = '';

    protected function conversacion(): ConversacionChatbot
    {
        if (! session()->has('chatbot.sesion')) {
            session(['chatbot.sesion' => (string) Str::uuid()]);
        }

        return app(ChatbotService::class)->conversacion(session('chatbot.sesion'), auth()->user());
    }

    public function enviar(): void
    {
        $this->mensaje = trim($this->mensaje);
        $this->validate(['mensaje' => ['required', 'string', 'max:500']]);
        $clave = 'chat-livewire:'.hash('sha256', (string) request()->ip());
        if (RateLimiter::tooManyAttempts($clave, 20)) {
            $this->addError('mensaje', 'Espera un momento antes de enviar otra pregunta.');

            return;
        }
        RateLimiter::hit($clave, 60);
        app(ChatbotService::class)->responder($this->conversacion(), $this->mensaje);
        $this->reset('mensaje');
        $this->dispatch('chat-actualizado');
    }

    public function sugerir(string $texto): void
    {
        $this->mensaje = $texto;
        $this->enviar();
    }

    public function actualizar(): void
    {
        $this->dispatch('chat-actualizado');
    }

    public function render()
    {
        $conversacion = $this->conversacion();

        return view('livewire.asistente-turistico', [
            'mensajes' => app(ChatbotService::class)->mensajesDesde($conversacion, 0), 'derivado' => (bool) $conversacion->der_hum,
        ]);
    }
}
