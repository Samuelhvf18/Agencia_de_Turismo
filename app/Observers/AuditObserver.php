<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->record('CREATE', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->record('UPDATE', $model, $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->record('DELETE', $model, $model->getAttributes(), null);
    }

    protected function record(string $action, Model $model, ?array $old, ?array $new): void
    {
        try {
            DB::table('bitacora_auditoria')->insert([
                'usr_id' => Auth::id(),
                'nom_tbl' => $model->getTable(),
                'id_reg' => $model->getKey() ?? 0,
                'acc' => $action,
                'val_ant' => $old ? json_encode($old) : null,
                'val_nvo' => $new ? json_encode($new) : null,
                'dir_ip' => Request::ip(),
                'age_usr' => Request::header('User-Agent'),
                'cre_en' => now(),
            ]);
        } catch (\Exception $e) {
            // Silenciar error si la tabla de auditoría aun no ha sido migrada
        }
    }
}
