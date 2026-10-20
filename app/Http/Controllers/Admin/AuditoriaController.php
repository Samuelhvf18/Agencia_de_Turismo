<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotificacionJob;
use App\Models\BitacoraAuditoria;
use App\Models\RegistroNotificacion;
use App\Models\Role;
use App\Models\User;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Seguridad y auditoría (M10, objetivo 7, RNF-02 a RNF-06): bitácora con
 * filtros y detalle antes/después, registro de notificaciones con reintento
 * y gestión de usuarios y roles. Solo administrador.
 */
class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate([
            'tabla' => ['nullable', 'string', 'max:100'],
            'acc' => ['nullable', 'in:CREATE,UPDATE,DELETE,READ'],
            'usuario' => ['nullable', 'string', 'max:150'],
            'id_reg' => ['nullable', 'integer'],
            'ip' => ['nullable', 'string', 'max:45'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $registros = BitacoraAuditoria::with('usuario')
            ->when($f['tabla'] ?? null, fn ($q, $v) => $q->where('nom_tbl', $v))
            ->when($f['acc'] ?? null, fn ($q, $v) => $q->where('acc', $v))
            ->when($f['id_reg'] ?? null, fn ($q, $v) => $q->where('id_reg', $v))
            ->when($f['ip'] ?? null, fn ($q, $v) => $q->where('dir_ip', $v))
            ->when($f['usuario'] ?? null, fn ($q, $v) => $q->whereHas('usuario', fn ($u) => $u->whereRaw('LOWER(cor) LIKE ?', ['%'.mb_strtolower($v).'%'])->orWhereRaw('LOWER(nom) LIKE ?', ['%'.mb_strtolower($v).'%'])))
            ->when($f['desde'] ?? null, fn ($q, $v) => $q->whereDate('cre_en', '>=', $v))
            ->when($f['hasta'] ?? null, fn ($q, $v) => $q->whereDate('cre_en', '<=', $v))
            ->orderByDesc('id')
            ->paginate(25)->withQueryString();

        return view('admin.panels.auditoria', [
            'registros' => $registros,
            'tablas' => BitacoraAuditoria::select('nom_tbl')->distinct()->orderBy('nom_tbl')->pluck('nom_tbl'),
            'resumen' => BitacoraAuditoria::where('cre_en', '>=', now()->subDays(7))->selectRaw('acc, COUNT(*) AS n')->groupBy('acc')->pluck('n', 'acc'),
            'filtros' => $f,
        ]);
    }

    public function show(BitacoraAuditoria $registro): View
    {
        $antes = $registro->val_ant ?? [];
        $despues = $registro->val_nvo ?? [];
        $campos = array_unique(array_merge(array_keys($antes), array_keys($despues)));
        sort($campos);

        return view('admin.auditoria.show', [
            'r' => $registro->load('usuario'),
            'cambios' => array_map(fn ($c) => [
                'campo' => $c,
                'antes' => $antes[$c] ?? null,
                'despues' => $despues[$c] ?? null,
                'cambio' => ($antes[$c] ?? null) !== ($despues[$c] ?? null),
            ], $campos),
            'historial' => BitacoraAuditoria::with('usuario')->where('nom_tbl', $registro->nom_tbl)->where('id_reg', $registro->id_reg)->orderByDesc('id')->limit(30)->get(),
        ]);
    }

    // ---- Notificaciones -------------------------------------------------------------------

    public function notificaciones(Request $request): View
    {
        $f = $request->validate(['est' => ['nullable', 'in:pendiente,enviado,fallido'], 'tip' => ['nullable', 'string', 'max:50']]);

        return view('admin.auditoria.notificaciones', [
            'registros' => RegistroNotificacion::with('reserva', 'usuario')
                ->when($f['est'] ?? null, fn ($q, $v) => $q->where('est', $v))
                ->when($f['tip'] ?? null, fn ($q, $v) => $q->where('tip', $v))
                ->orderByDesc('id')->paginate(25)->withQueryString(),
            'resumen' => RegistroNotificacion::selectRaw('est, COUNT(*) AS n')->groupBy('est')->pluck('n', 'est'),
            'tipos' => NotificacionService::TIPOS,
            'filtros' => $f,
            'n8n' => (bool) config('services.n8n.webhook_notificaciones'),
        ]);
    }

    public function reintentar(RegistroNotificacion $registro): RedirectResponse
    {
        abort_unless($registro->est === 'fallido', 422, 'Solo se reintentan notificaciones fallidas.');
        $registro->update(['est' => 'pendiente']);
        NotificacionJob::dispatch($registro->id);

        return back()->with('estado', "Notificación #{$registro->id} enviada otra vez a la cola.");
    }

    // ---- Usuarios y roles -------------------------------------------------------------------

    public function usuarios(Request $request): View
    {
        $q = trim((string) $request->input('q'));

        return view('admin.auditoria.usuarios', [
            'usuarios' => User::with('rol')
                ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->whereRaw('LOWER(cor) LIKE ?', ['%'.mb_strtolower($q).'%'])->orWhereRaw('LOWER(nom) LIKE ?', ['%'.mb_strtolower($q).'%'])))
                ->orderBy('nom')->paginate(25)->withQueryString(),
            'roles' => Role::orderBy('id')->get(),
            'q' => $q,
            'porRol' => User::join('roles', 'roles.id', '=', 'users.rol_id')->select('roles.nom', DB::raw('COUNT(*) AS n'))->groupBy('roles.nom')->pluck('n', 'nom'),
        ]);
    }

    public function actualizarUsuario(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate(['rol_id' => ['required', 'integer', 'exists:roles,id'], 'act' => ['required', 'boolean']]);

        if ($usuario->id === $request->user()->id && (! $datos['act'] || (int) $datos['rol_id'] !== $usuario->rol_id)) {
            return back()->withErrors(['usuario' => 'No puedes quitarte el rol de administrador ni desactivar tu propia cuenta.']);
        }

        $usuario->update(['rol_id' => $datos['rol_id'], 'act' => (bool) $datos['act']]);

        return back()->with('estado', "Usuario {$usuario->cor} actualizado.");
    }
}
