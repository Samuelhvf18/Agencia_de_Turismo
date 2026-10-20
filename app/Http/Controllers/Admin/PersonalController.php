<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservas\ParticipantesRequest;
use App\Models\Idioma;
use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\Role;
use App\Models\SalidaTour;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Personal de campo (M03, RF-15): guías con idiomas, experiencia y límite
 * semanal de horas, y choferes con licencia. No se desactiva a quien tiene
 * salidas futuras asignadas.
 */
class PersonalController extends Controller
{
    public function index(): View
    {
        $hoy = today()->toDateString();

        return view('admin.panels.personal', [
            'guias' => PerfilGuia::with('usuario', 'idiomas')
                ->withCount(['salidas as futuras' => fn ($q) => $q->where('est', SalidaTour::EST_PROGRAMADA)->where('fec_sal', '>=', $hoy)])
                ->get()->sortBy('usuario.nom'),
            'choferes' => PerfilChofer::with('usuario')
                ->withCount(['salidas as futuras' => fn ($q) => $q->where('est', SalidaTour::EST_PROGRAMADA)->where('fec_sal', '>=', $hoy)])
                ->get()->sortBy('usuario.nom'),
            'idiomas' => Idioma::orderBy('nom')->get(),
        ]);
    }

    public function edit(string $tipo, int $id): View
    {
        $perfil = $this->perfil($tipo, $id)->load('usuario');

        return view('admin.personal.edit', [
            'tipo' => $tipo,
            'perfil' => $perfil,
            'idiomas' => Idioma::orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request, string $tipo): RedirectResponse
    {
        abort_unless(in_array($tipo, ['guia', 'chofer'], true), 404);
        $datos = $request->validate($this->reglas($tipo) + [
            'cor' => ['required', 'email', 'max:150', 'unique:users,cor'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ]);

        DB::transaction(function () use ($datos, $tipo) {
            $usuario = User::create([
                'rol_id' => Role::where('nom', $tipo)->value('id'),
                'nom' => $datos['nom'],
                'cor' => strtolower($datos['cor']),
                'tel' => $datos['tel'] ?? null,
                'clv' => $datos['password'],
                'ci_cmp' => '',
                'act' => true,
            ]);
            $this->guardarPerfil($tipo, $usuario, $datos);
        });

        return redirect()->route('admin.personal')->with('estado', ucfirst($tipo === 'guia' ? 'guía' : 'chofer').' registrado.');
    }

    public function update(Request $request, string $tipo, int $id): RedirectResponse
    {
        $perfil = $this->perfil($tipo, $id);
        $datos = $request->validate($this->reglas($tipo) + [
            'cor' => ['required', 'email', 'max:150', Rule::unique('users', 'cor')->ignore($perfil->usr_id)],
        ]);

        DB::transaction(function () use ($perfil, $datos, $tipo) {
            $perfil->usuario->update(['nom' => $datos['nom'], 'cor' => strtolower($datos['cor']), 'tel' => $datos['tel'] ?? null]);
            $this->guardarPerfil($tipo, $perfil->usuario, $datos);
        });

        return redirect()->route('admin.personal')->with('estado', 'Datos actualizados.');
    }

    /** Activa o desactiva; la regla de negocio impide desactivar con salidas futuras. */
    public function estado(string $tipo, int $id): RedirectResponse
    {
        $perfil = $this->perfil($tipo, $id);

        if ($perfil->act) {
            $futuras = $perfil->salidas()->where('est', SalidaTour::EST_PROGRAMADA)->whereDate('fec_sal', '>=', today())->count();
            if ($futuras > 0) {
                return back()->withErrors(['personal' => "No se puede desactivar: tiene {$futuras} salida(s) futura(s) asignada(s). Reasígnalas primero."]);
            }
        }

        $perfil->update(['act' => ! $perfil->act]);
        $perfil->usuario->update(['act' => $perfil->act]);

        return back()->with('estado', $perfil->act ? 'Reactivado.' : 'Desactivado.');
    }

    private function reglas(string $tipo): array
    {
        $comunes = [
            'nom' => ['required', 'string', 'max:150', ParticipantesRequest::NOMBRE],
            'tel' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s-]{7,20}$/'],
        ];

        return $comunes + ($tipo === 'guia' ? [
            'ani_exp' => ['required', 'integer', 'min:0', 'max:60'],
            'lim_hrs_sem' => ['required', 'integer', 'min:1', 'max:60'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'idiomas' => ['required', 'array', 'min:1'],
            'idiomas.*' => ['integer', 'exists:idiomas,id'],
        ] : [
            'num_lic' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9-]+$/i'],
            'tip_lic' => ['required', 'in:A,B,C,P,M,T'],
            'fec_ven_lic' => ['required', 'date', 'after:today'],
        ]);
    }

    private function guardarPerfil(string $tipo, User $usuario, array $datos): void
    {
        if ($tipo === 'guia') {
            $perfil = PerfilGuia::updateOrCreate(['usr_id' => $usuario->id], [
                'ani_exp' => $datos['ani_exp'],
                'lim_hrs_sem' => $datos['lim_hrs_sem'],
                'bio' => $datos['bio'] ?? null,
                'rta_amg' => PerfilGuia::where('usr_id', $usuario->id)->value('rta_amg') ?? Str::slug($usuario->nom).'-'.$usuario->id,
            ]);
            $perfil->idiomas()->sync($datos['idiomas']);

            return;
        }

        PerfilChofer::updateOrCreate(['usr_id' => $usuario->id], [
            'num_lic' => strtoupper($datos['num_lic']),
            'tip_lic' => $datos['tip_lic'],
            'fec_ven_lic' => $datos['fec_ven_lic'],
        ]);
    }

    private function perfil(string $tipo, int $id): PerfilGuia|PerfilChofer
    {
        return match ($tipo) {
            'guia' => PerfilGuia::findOrFail($id),
            'chofer' => PerfilChofer::findOrFail($id),
            default => abort(404),
        };
    }
}
