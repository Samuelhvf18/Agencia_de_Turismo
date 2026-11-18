<?php

namespace App\Services\Catalogo;

use App\Models\FotoTour;
use App\Models\Tour;
use App\Support\ReglasEntrada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class GaleriaTours
{
    public const MAX_FOTOS = 30;

    public function agregar(Tour $tour, array $archivos, ?string $texto = null): void
    {
        Validator::make(['fotos' => $archivos, 'texto' => $texto], [
            'fotos' => ['required', 'array', 'min:1', 'max:10'], 'fotos.*' => ReglasEntrada::imagen(),
            'texto' => ['nullable', 'string', 'max:150'],
        ])->validate();
        $guardados = [];
        try {
            DB::transaction(function () use ($tour, $archivos, $texto, &$guardados) {
                Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();
                if ($tour->fotos()->count() + count($archivos) > self::MAX_FOTOS) {
                    throw ValidationException::withMessages(['fotos' => 'Cada tour admite hasta 30 fotos. Elimina alguna antes de subir más.']);
                }
                $orden = (int) $tour->fotos()->max('ord_vis');
                foreach ($archivos as $archivo) {
                    $ruta = $archivo->store("tours/{$tour->id}", 'public');
                    $guardados[] = $ruta;
                    $tour->fotos()->create(['rta_arc' => $ruta, 'ord_vis' => ++$orden, 'txt_atv' => $texto ?: $tour->nom]);
                }
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($guardados);
            throw $e;
        }
    }

    public function ordenar(Tour $tour, array $orden, array $textos = []): void
    {
        $orden = array_map('intval', array_values($orden));
        DB::transaction(function () use ($tour, $orden, $textos) {
            Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();
            $ids = $tour->fotos()->pluck('id')->sort()->values()->all();
            $recibidos = collect($orden)->sort()->values()->all();
            if ($ids !== $recibidos || count(array_unique($orden)) !== count($orden)) {
                throw ValidationException::withMessages(['orden' => 'La galería cambió. Recarga la página y vuelve a ordenar todas sus fotos.']);
            }
            foreach ($orden as $i => $id) {
                $tour->fotos()->whereKey($id)->update(['ord_vis' => $i + 1] +
                    (isset($textos[$id]) ? ['txt_atv' => $textos[$id]] : []));
            }
        });
    }

    public function eliminar(Tour $tour, FotoTour $foto): void
    {
        abort_unless($foto->tur_id === $tour->id, 404);
        DB::transaction(function () use ($tour, $foto) {
            Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();
            if ($tour->act && $tour->fotos()->count() <= 1) {
                throw ValidationException::withMessages(['fotos' => 'Un tour publicado necesita una foto. Agrega otra antes de eliminar la portada o desactiva el tour.']);
            }
            $foto->delete();
            DB::afterCommit(function () use ($foto) {
                if (str_starts_with($foto->rta_arc, 'tours/')) {
                    Storage::disk('public')->delete($foto->rta_arc);
                }
            });
        });
    }
}
