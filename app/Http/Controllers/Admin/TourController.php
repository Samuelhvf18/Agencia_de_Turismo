<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Models\Tour;
use App\Models\CategoriaTour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with('categoria')->latest('cre_en')->get();
        $categorias = CategoriaTour::all();

        return view('admin.panels.tours', compact('tours', 'categorias'));
    }

    public function store(StoreTourRequest $request)
    {
        $data = $request->validated();
        
        // Generar slug para cumplir con la restricción NOT NULL de la BD
        $data['rta_amg'] = Str::slug($data['nom']) . '-' . time();
        $data['act'] = filter_var($request->input('act', true), FILTER_VALIDATE_BOOLEAN);

        $tour = Tour::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tour creado correctamente.',
            'data' => $tour
        ], 201);
    }

    public function show(Tour $tour)
    {
        return response()->json($tour->load('categoria'));
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'cat_id'  => 'required|exists:categorias_tour,id',
            'cod'     => 'required|string|max:20|unique:tours,cod,' . $tour->id,
            'nom'     => 'required|string|max:150',
            'des'     => 'nullable|string',
            'niv_dif' => 'required|in:facil,moderado,dificil,experto',
            'dur_hrs' => 'required|numeric|between:1,14',
            'pre_bas' => 'required|numeric|min:0',
            'cap_max' => 'required|integer|min:1|max:25',
            'act'     => 'boolean',
        ],[
        'cap_max.max' => 'La capacidad máxima no puede ser mayor a 25 personas.',
        'cap_max.min' => 'La capacidad máxima debe ser de al menos 1 persona.',
    ]);

        $validated['rta_amg'] = Str::slug($validated['nom']) . '-' . $tour->id;
        $validated['act'] = filter_var($request->input('act', true), FILTER_VALIDATE_BOOLEAN);

        $tour->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tour actualizado correctamente.'
        ]);
    }

    public function toggleStatus(Tour $tour)
{
    $tour->update([
        'act' => !$tour->act
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Estado del tour actualizado correctamente.',
        'act' => $tour->act
    ]);
}

    public function destroy(Tour $tour)
    {
        $tour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour eliminado correctamente.'
        ]);
    }
}
    