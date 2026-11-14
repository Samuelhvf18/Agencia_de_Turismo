<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourRequest;
use App\Models\CategoriaTour;
use App\Models\FotoTour;
use App\Models\PuntoRuta;
use App\Models\Tour;
use App\Services\Catalogo\SlugService;
use App\Services\CodeGeneratorService;
use App\Support\GeoJsonRuta;
use App\Support\PresentadorTour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Administración del catálogo (M02, HU 1 a 4, RF-37): categorías, tours con
 * ruta amigable única, galería de fotos y editor de trazado y puntos de interés.
 */
class CatalogoAdminController extends Controller
{
    // ---- Tours --------------------------------------------------------------------

    public function index(): View
    {
        return view('admin.panels.tours', [
            'tours' => Tour::with('categoria')->withCount(['fotos', 'puntosRuta', 'salidas'])->whereNull('eli_en')->orderBy('nom')->get(),
            'categorias' => CategoriaTour::withCount('tours')->orderBy('nom')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalogo.form', ['tour' => new Tour(['act' => true, 'niv_dif' => 'moderado']), 'categorias' => CategoriaTour::orderBy('nom')->get()]);
    }

    public function store(TourRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $tour = Tour::create($datos + [
            'cod' => CodeGeneratorService::generate('TUR'),
            'rta_amg' => SlugService::unico($datos['nom'], 'tours'),
        ]);

        return redirect()->route('admin.catalogo.edit', $tour)->with('estado', "Tour creado con la ruta /tours/{$tour->rta_amg}. Ahora agrega fotos y el trazado.");
    }

    public function edit(Tour $tour): View
    {
        $tour->load(['fotos' => fn ($q) => $q->orderBy('ord_vis'), 'puntosRuta']);

        return view('admin.catalogo.form', [
            'tour' => $tour,
            'categorias' => CategoriaTour::orderBy('nom')->get(),
            'fotos' => $tour->fotos->map(fn (FotoTour $f) => ['foto' => $f, 'url' => PresentadorTour::urlFoto($f->rta_arc)]),
        ]);
    }

    public function update(TourRequest $request, Tour $tour): RedirectResponse
    {
        $datos = $request->validated();
        // La ruta amigable se conserva para no romper enlaces; se regenera solo si se pide.
        if ($request->boolean('regenerar_ruta')) {
            $datos['rta_amg'] = SlugService::unico($datos['nom'], 'tours', $tour->id);
        }
        $tour->update($datos);

        return back()->with('estado', 'Tour actualizado.');
    }

    /** Baja lógica: el tour deja de publicarse, pero se conserva su historial de reservas. */
    public function destroy(Tour $tour): RedirectResponse
    {
        $futuras = $tour->salidas()->where('est', 'programada')->whereDate('fec_sal', '>=', today())->whereHas('reservas', fn ($q) => $q->whereIn('est', ['pendiente', 'confirmada']))->count();
        if ($futuras) {
            return back()->withErrors(['tour' => "El tour tiene {$futuras} salida(s) futura(s) con reservas activas. Cancélalas antes de darlo de baja."]);
        }
        $tour->update(['act' => false, 'eli_en' => now()]);

        return redirect()->route('admin.tours')->with('estado', "Tour {$tour->nom} dado de baja.");
    }

    // ---- Categorías -------------------------------------------------------------------

    public function guardarCategoria(Request $request, ?CategoriaTour $categoria = null): RedirectResponse
    {
        $datos = $request->validate([
            'nom' => ['required', 'string', 'max:80', Rule::unique('categorias_tour', 'nom')->ignore($categoria?->id)],
            'des' => ['nullable', 'string', 'max:500'],
            'ico' => ['nullable', 'string', 'max:255', 'regex:/^fa-[a-z]+ fa-[a-z0-9-]+$/'],
        ], ['ico.regex' => 'El ícono debe ser una clase de Font Awesome, p. ej. «fa-solid fa-person-hiking».']);

        if ($categoria) {
            $categoria->update($datos);
        } else {
            CategoriaTour::create($datos + ['rta_amg' => SlugService::unico($datos['nom'], 'categorias_tour')]);
        }

        return back()->with('estado', 'Categoría guardada.');
    }

    public function eliminarCategoria(CategoriaTour $categoria): RedirectResponse
    {
        if ($categoria->tours()->exists()) {
            return back()->withErrors(['categoria' => 'La categoría tiene tours; muévelos a otra antes de eliminarla.']);
        }
        $categoria->delete();

        return back()->with('estado', 'Categoría eliminada.');
    }

    // ---- Galería ------------------------------------------------------------------------

    public function subirFotos(Request $request, Tour $tour): RedirectResponse
    {
        $request->validate([
            'fotos' => ['required', 'array', 'max:10'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'txt_atv' => ['nullable', 'string', 'max:150'],
        ]);

        $orden = (int) $tour->fotos()->max('ord_vis');
        foreach ($request->file('fotos') as $archivo) {
            $tour->fotos()->create([
                'rta_arc' => $archivo->store("tours/{$tour->id}", 'public'),
                'ord_vis' => ++$orden,
                'txt_atv' => $request->input('txt_atv') ?: $tour->nom,
            ]);
        }

        return back()->with('estado', 'Fotos agregadas a la galería.');
    }

    /** Recibe el nuevo orden: fotos[] = ids en el orden deseado; también actualiza el texto alternativo. */
    public function ordenarFotos(Request $request, Tour $tour): RedirectResponse
    {
        $datos = $request->validate([
            'orden' => ['required', 'array'],
            'orden.*' => ['integer', Rule::exists('fotos_tour', 'id')->where('tur_id', $tour->id)],
            'txt' => ['nullable', 'array'],
            'txt.*' => ['nullable', 'string', 'max:150'],
        ]);

        DB::transaction(function () use ($datos, $tour) {
            foreach (array_values($datos['orden']) as $i => $id) {
                $tour->fotos()->whereKey($id)->update(['ord_vis' => $i + 1] + (isset($datos['txt'][$id]) ? ['txt_atv' => $datos['txt'][$id]] : []));
            }
        });

        return back()->with('estado', 'Galería actualizada; la primera foto es la portada.');
    }

    public function eliminarFoto(Tour $tour, FotoTour $foto): RedirectResponse
    {
        abort_unless($foto->tur_id === $tour->id, 404);
        if (! preg_match('#^(https?://|images/)#', $foto->rta_arc)) {
            Storage::disk('public')->delete($foto->rta_arc);
        }
        $foto->delete();

        return back()->with('estado', 'Foto eliminada.');
    }

    // ---- Trazado y puntos de interés ---------------------------------------------------------

    /**
     * Guarda los puntos del editor de mapa (reemplaza el conjunto) y el
     * trazado GeoJSON (pegado o generado desde los puntos).
     */
    public function guardarRuta(Request $request, Tour $tour): RedirectResponse
    {
        $datos = $request->validate([
            'puntos' => ['present', 'array', 'max:200'],
            'puntos.*.nom' => ['nullable', 'string', 'max:120'],
            'puntos.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'puntos.*.lon' => ['required', 'numeric', 'between:-180,180'],
            'puntos.*.alt_m' => ['nullable', 'integer', 'between:0,7000'],
            'puntos.*.tip_pun' => ['required', 'in:inicio,checkpoint,mirador,fin'],
            'trazado' => ['nullable', 'string', 'max:2000000'],
            'trazado_desde_puntos' => ['nullable', 'boolean'],
        ]);

        $trazado = null;
        if (! empty($datos['trazado'])) {
            $trazado = json_decode($datos['trazado'], true);
            if (! is_array($trazado)) {
                throw ValidationException::withMessages(['trazado' => __('El trazado debe contener líneas GeoJSON válidas con coordenadas geográficas.')]);
            }
            $trazado = GeoJsonRuta::validar($trazado);
        } elseif ($request->boolean('trazado_desde_puntos') && count($datos['puntos']) > 1) {
            $trazado = ['type' => 'LineString', 'coordinates' => array_map(
                fn ($p) => array_filter([(float) $p['lon'], (float) $p['lat'], isset($p['alt_m']) ? (int) $p['alt_m'] : null], fn ($v) => $v !== null),
                $datos['puntos']
            )];
        }

        DB::transaction(function () use ($tour, $datos, $trazado, $request) {
            $tour->puntosRuta()->delete();
            foreach (array_values($datos['puntos']) as $i => $p) {
                PuntoRuta::create(['tur_id' => $tour->id, 'ord_sec' => $i + 1] + $p);
            }
            if ($trazado !== null || $request->boolean('quitar_trazado')) {
                $tour->update(['trz_geo' => $trazado]);
            }
            // Altitudes del tour a partir de los puntos, si se registraron.
            $alts = array_filter(array_column($datos['puntos'], 'alt_m'), fn ($a) => $a !== null);
            if ($alts) {
                $tour->update(['alt_min_m' => min($alts), 'alt_max_m' => max($alts)]);
            }
        });

        return back()->with('estado', count($datos['puntos']).' punto(s) de ruta guardados.');
    }
}
