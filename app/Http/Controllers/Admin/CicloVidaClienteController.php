<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarkovEstadoCliente;
use App\Services\Io\IoDemoData;
use App\Services\Io\MarkovService;
use App\Services\Io\PanelMarkovBuilder;
use Illuminate\Http\Request;

/**
 * Panel «Ciclo de vida del cliente» (Módulo 07 — Cadena de Markov absorbente).
 */
class CicloVidaClienteController extends Controller
{
    public function __construct(private MarkovService $markov) {}

    public function index(Request $request)
    {
        $inicio = $request->validate(['inicio' => ['nullable', 'in:prospecto,reservado,completado']])['inicio'] ?? null;

        if (! MarkovEstadoCliente::exists()) {
            return view('admin.io.ciclo-vida', array_merge(app(IoDemoData::class)->cicloVida($inicio),
                ['formAction' => route('admin.io.ciclo-vida'),
                   'avisoDemo' => 'Todavía no hay estados de Markov registrados. Ejecute php artisan db:seed --class=IoColasMarkovSeeder y php artisan io:recalibrar-markov --sincronizar.']));
        }

        $vigente = $this->markov->matrizVigente();

        return view('admin.io.ciclo-vida', PanelMarkovBuilder::armar(
            $this->markov,
            $vigente['P'],
            $vigente['fec_cal'],
            $this->markov->conteoActual(),
            $inicio,
            $this->markov->cohortesParaPanel()->all(),
        ) + ['demo' => false, 'formAction' => route('admin.io.ciclo-vida')]);
    }
}
