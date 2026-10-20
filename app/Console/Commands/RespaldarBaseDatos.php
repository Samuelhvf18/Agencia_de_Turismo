<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * RNF-12: respaldo diario de PostgreSQL con pg_dump (formato custom,
 * comprimido) en storage/backups, conservando los últimos 30 días.
 * Restaurar: pg_restore -d agencia_turismo_db --clean storage/backups/<archivo>.dump
 */
class RespaldarBaseDatos extends Command
{
    protected $signature = 'respaldo:bd {--dias=30 : Días de respaldos a conservar} {--pg-dump= : Ruta de pg_dump (por defecto, PG_DUMP_PATH o "pg_dump")}';

    protected $description = 'Respalda la base de datos con pg_dump y elimina los respaldos de más de 30 días';

    public function handle(): int
    {
        $c = config('database.connections.'.config('database.default'));
        if (($c['driver'] ?? null) !== 'pgsql') {
            $this->error('El respaldo con pg_dump requiere la conexión pgsql.');

            return self::FAILURE;
        }

        $dir = storage_path('backups');
        File::ensureDirectoryExists($dir);
        $archivo = $dir.DIRECTORY_SEPARATOR.$c['database'].'_'.now()->format('Ymd_His').'.dump';

        $proceso = new Process([
            $this->option('pg-dump') ?: env('PG_DUMP_PATH', 'pg_dump'),
            '-h', (string) $c['host'], '-p', (string) $c['port'], '-U', (string) $c['username'],
            '-F', 'c', '-f', $archivo, (string) $c['database'],
        ], null, ['PGPASSWORD' => (string) $c['password']], null, 600);
        $proceso->run();

        if (! $proceso->isSuccessful()) {
            $this->error('pg_dump falló: '.trim($proceso->getErrorOutput()));

            return self::FAILURE;
        }

        $limite = now()->subDays(max(1, (int) $this->option('dias')))->getTimestamp();
        $borrados = 0;
        foreach (File::glob($dir.DIRECTORY_SEPARATOR.'*.dump') as $viejo) {
            if (File::lastModified($viejo) < $limite) {
                File::delete($viejo);
                $borrados++;
            }
        }

        $this->info('Respaldo creado: '.basename($archivo).' ('.number_format(File::size($archivo) / 1024, 1)." KB). Respaldos antiguos eliminados: {$borrados}.");

        return self::SUCCESS;
    }
}
