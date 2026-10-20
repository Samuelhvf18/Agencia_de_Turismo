<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversacionChatbot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Atención humana de las conversaciones derivadas por el chatbot (RF-31). */
class ChatbotAdminController extends Controller
{
    public function index(Request $request): View
    {
        $soloDerivadas = $request->boolean('derivadas', true);

        return view('admin.chatbot.index', [
            'conversaciones' => ConversacionChatbot::with('usuario')
                ->withCount('mensajes')
                ->when($soloDerivadas, fn ($q) => $q->where('der_hum', true))
                ->orderByDesc('der_hum')->orderByDesc('ini_en')
                ->paginate(20)->withQueryString(),
            'soloDerivadas' => $soloDerivadas,
            'pendientes' => ConversacionChatbot::where('der_hum', true)->whereNull('fin_en')->count(),
        ]);
    }

    public function show(ConversacionChatbot $conversacion): View
    {
        return view('admin.chatbot.show', ['c' => $conversacion->load('usuario', 'mensajes')]);
    }

    public function responder(Request $request, ConversacionChatbot $conversacion): RedirectResponse
    {
        $datos = $request->validate(['mensaje' => ['required', 'string', 'max:2000']]);
        $conversacion->mensajes()->create(['rem' => 'bot', 'txt_msj' => $datos['mensaje'], 'int_det' => 'humano']);

        return back()->with('estado', 'Respuesta enviada al cliente.');
    }

    /** Cierra la atención humana: el chatbot vuelve a responder automáticamente. */
    public function cerrar(ConversacionChatbot $conversacion): RedirectResponse
    {
        $conversacion->update(['der_hum' => false]);

        return redirect()->route('admin.chatbot')->with('estado', 'Conversación devuelta al asistente automático.');
    }
}
