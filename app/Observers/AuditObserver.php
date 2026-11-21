<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/** Registro de cambios del sistema. */
class AuditObserver
{
    /** Excluye columnas sensibles. */
    private const OCULTOS = ['clv', 'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'token'];

    public function created(Model $model): void
    {
        $this->registrar('CREATE', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $cambios = $model->getChanges();
        $anteriores = [];
        foreach (array_keys($cambios) as $campo) {
            $anteriores[$campo] = $model->getOriginal($campo);
        }

        $this->registrar('UPDATE', $model, $anteriores, $cambios);
    }

    public function deleted(Model $model): void
    {
        $this->registrar('DELETE', $model, $model->getAttributes(), null);
    }

    /** Audita lecturas sensibles. */
    public static function lectura(Model $model): void
    {
        (new self)->registrar('READ', $model, null, null);
    }

    protected function registrar(string $accion, Model $model, ?array $anterior, ?array $nuevo): void
    {
        $usuarioId = Auth::id();
        // Usuario eliminado sin referencia.
        if ($accion === 'DELETE' && $model->getTable() === 'users' && $usuarioId === $model->getKey()) {
            $usuarioId = null;
        }

        try {
            // SAVEPOINT aísla errores de auditoría.
            DB::transaction(fn () => DB::table('bitacora_auditoria')->insert([
                'usr_id' => $usuarioId,
                'nom_tbl' => $model->getTable(),
                'id_reg' => (int) ($model->getKey() ?? 0),
                'acc' => $accion,
                'val_ant' => $this->json($anterior),
                'val_nvo' => $this->json($nuevo),
                'dir_ip' => Request::ip(),
                'age_usr' => Request::header('User-Agent'),
                'cre_en' => now(),
            ]));
        } catch (\Throwable $e) {
            // Lecturas requieren auditoría exitosa.
            Log::error('No se pudo registrar la auditoría', [
                'tabla' => $model->getTable(),
                'id' => $model->getKey(),
                'accion' => $accion,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function json(?array $valores): ?string
    {
        if ($valores === null) {
            return null;
        }

        return json_encode(array_diff_key($valores, array_flip(self::OCULTOS)), JSON_UNESCAPED_UNICODE);
    }
}
