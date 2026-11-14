<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

/** Respaldo privado de BD, adjuntos y manifiesto SHA-256. Ver docs/OPERACION.md. */
class RespaldarBaseDatos extends Command
{
    protected $signature = 'respaldo:bd {--dias=30 : Días de respaldos a conservar} {--pg-dump= : Ruta de pg_dump} {--sin-archivos : Excluir adjuntos privados explícitamente}';

    protected $description = 'Respalda PostgreSQL y archivos privados; conserva los últimos 30 días';

    public function handle(): int
    {
        $c = config('database.connections.'.config('database.default'));
        if (($c['driver'] ?? null) !== 'pgsql') {
            $this->error('El respaldo con pg_dump requiere la conexión pgsql.');

            return self::FAILURE;
        }
        $dir = storage_path('backups');
        File::ensureDirectoryExists($dir, 0700);
        $base = $dir.DIRECTORY_SEPARATOR.basename($c['database']).'_'.now()->format('Ymd_His').'_'.bin2hex(random_bytes(3));
        $dump = $base.'.dump';
        $zip = $base.'.zip';
        $manifestPath = $base.'.json';
        $archive = null;
        try {
            $proceso = new Process([
                $this->option('pg-dump') ?: env('PG_DUMP_PATH', 'pg_dump'),
                '-h', (string) $c['host'], '-p', (string) $c['port'], '-U', (string) $c['username'],
                '-F', 'c', '-f', $dump, (string) $c['database'],
            ], null, ['PGPASSWORD' => (string) $c['password']], null, 600);
            $proceso->mustRun();
            chmod($dump, 0600);
            $manifest = ['version' => 1, 'fecha' => now()->toIso8601String(), 'base' => $c['database'],
                'dump' => ['archivo' => basename($dump), 'sha256' => hash_file('sha256', $dump)],
                'archivos_incluidos' => ! $this->option('sin-archivos'), 'archivos' => []];
            if (! $this->option('sin-archivos')) {
                $archive = new ZipArchive;
                if ($archive->open($zip, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
                    throw new \RuntimeException('No se pudo crear el archivo de adjuntos.');
                }
                $root = storage_path('app/private');
                File::ensureDirectoryExists($root);
                foreach (File::allFiles($root) as $file) {
                    if ($file->isLink()) {
                        throw new \RuntimeException('Un adjunto es un enlace simbólico; revise el almacenamiento.');
                    }
                    $relative = $file->getRelativePathname();
                    if ($relative === '.gitignore') {
                        continue;
                    }
                    $manifest['archivos'][$relative] = hash_file('sha256', $file->getPathname());
                    if (! $archive->addFile($file->getPathname(), $relative)) {
                        throw new \RuntimeException('No se pudo añadir un adjunto.');
                    }
                }
                // ZipArchive no crea un ZIP vacío sin una entrada.
                $archive->addFromString('RESPALDO.txt', 'Adjuntos privados. Verifique sus hashes con el manifiesto JSON.');
                if (! $archive->close()) {
                    throw new \RuntimeException('No se pudo cerrar el archivo de adjuntos.');
                }
                $archive = null;
                chmod($zip, 0600);
                $manifest['zip'] = ['archivo' => basename($zip), 'sha256' => hash_file('sha256', $zip)];
            }
            File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            chmod($manifestPath, 0600);
        } catch (\Throwable $e) {
            if ($archive) {
                $archive->close();
            }
            File::delete([$dump, $zip, $manifestPath]);
            $this->error('El respaldo no se completó: '.$e->getMessage());

            return self::FAILURE;
        }
        $limite = now()->subDays(max(1, (int) $this->option('dias')))->getTimestamp();
        $borrados = 0;
        foreach (File::files($dir) as $viejo) {
            if (in_array($viejo->getExtension(), ['dump', 'zip', 'json'], true) && $viejo->getMTime() < $limite) {
                File::delete($viejo->getPathname());
                $borrados++;
            }
        }
        $this->info('Respaldo completo: '.basename($manifestPath).'. Archivos antiguos eliminados: '.$borrados.'.');

        return self::SUCCESS;
    }
}
