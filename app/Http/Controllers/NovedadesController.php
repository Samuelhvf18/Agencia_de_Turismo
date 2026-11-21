<?php

namespace App\Http\Controllers;

use App\Services\Notificaciones\CorreoTransaccional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class NovedadesController extends Controller
{
    public function store(Request $request, CorreoTransaccional $correo)
    {
        $datos = $request->validate(['correo' => ['required', 'email:rfc', 'max:150'], 'consentimiento' => ['accepted']]);
        $direccion = Str::lower(trim($datos['correo']));
        $subs = DB::table('suscripciones_novedades')->where('correo', $direccion)->first();
        if (! $subs || $subs->baja_en || ! $subs->confirmada_en) {
            // Un reintento no revoca una suscripción activa ni permite envíos masivos.
            if (! $subs || now()->diffInSeconds($subs->updated_at, absolute: true) >= 60) {
                DB::table('suscripciones_novedades')->updateOrInsert(['correo' => $direccion], [
                    'consentimiento_en' => now(), 'version_politica' => 'privacidad-2026-11',
                    'confirmada_en' => null, 'baja_en' => null,
                    'created_at' => $subs?->created_at ?? now(), 'updated_at' => now(),
                ]);
                $subs = DB::table('suscripciones_novedades')->where('correo', $direccion)->first();
                $url = URL::temporarySignedRoute('novedades.confirmar', now()->addDay(), ['id' => $subs->id, 'version' => $subs->consentimiento_en]);
                try {
                    $correo->enviar($direccion, 'Confirma tu suscripción a Andinist Bolivia', "Solicitaste recibir novedades. Confirma aquí:\n{$url}\n\nVence en 24 horas. Si no fuiste tú, ignora este mensaje: no te suscribiremos.");
                } catch (\Throwable $e) {
                    report($e);

                    return back()->withErrors(['novedades' => 'No se pudo enviar la confirmación. Intenta de nuevo más tarde.']);
                }
            }
        }

        return redirect()->to(route('home').'#footer-novedades')->with('novedades', 'Revisa tu correo para confirmar la suscripción. Si ya estás suscrito, no necesitas hacer nada.');
    }

    public function confirmar(Request $request, int $id)
    {
        $subs = DB::table('suscripciones_novedades')->find($id);
        abort_unless($subs && ! $subs->baja_en && hash_equals($subs->consentimiento_en, (string) $request->query('version')), 410);
        if ($request->isMethod('post')) {
            DB::table('suscripciones_novedades')->where('id', $id)->whereNull('confirmada_en')->update(['confirmada_en' => now(), 'updated_at' => now()]);
        }

        return view('novedades.confirmar', ['confirmada' => $request->isMethod('post') || (bool) $subs->confirmada_en]);
    }

    /** La baja requiere POST para que un escáner de enlaces no la ejecute. */
    public function baja(Request $request, int $id)
    {
        abort_unless(DB::table('suscripciones_novedades')->where('id', $id)->exists(), 404);
        if ($request->isMethod('post')) {
            DB::table('suscripciones_novedades')->where('id', $id)->update(['baja_en' => now(), 'updated_at' => now()]);
        }

        return view('novedades.baja', ['hecho' => $request->isMethod('post')]);
    }
}
