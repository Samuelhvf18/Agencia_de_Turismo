<?php

namespace App\Console\Commands;

use App\Jobs\NotificacionJob;
use App\Models\RegistroNotificacion;
use App\Services\Notificaciones\EntregaNotificacion;
use Illuminate\Console\Command;

class RecuperarNotificaciones extends Command
{
    protected $signature = 'notificaciones:recuperar {--limite=200}';

    protected $description = 'Reencola eventos pendientes o fallidos, incluidos pagos confirmados antes de una caída';

    public function handle(): int
    {
        $registros = RegistroNotificacion::whereIn('est', ['pendiente', 'fallido'])
            ->where('intentos', '<', EntregaNotificacion::MAX_INTENTOS)
            ->where(fn ($q) => $q->whereNull('disponible_en')->orWhere('disponible_en', '<=', now()))
            ->where(fn ($q) => $q->whereNull('bloqueado_hasta')->orWhere('bloqueado_hasta', '<=', now()))
            ->orderBy('id')->limit(max(1, min(1000, (int) $this->option('limite'))))->get();
        foreach ($registros as $registro) {
            NotificacionJob::dispatch($registro->id);
        }
        $this->info($registros->count().' eventos reencolados.');

        return self::SUCCESS;
    }
}
