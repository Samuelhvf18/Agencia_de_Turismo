<?php

namespace App\Providers;

use App\Models;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Modelos de negocio cuyas altas, cambios y bajas quedan en bitacora_auditoria.
     * No se auditan la propia bitácora ni los registros de alto volumen
     * (mensajes del chatbot, eventos y métricas de colas).
     */
    private const MODELOS_AUDITADOS = [
        Models\User::class,
        Models\Role::class,
        Models\PerfilGuia::class,
        Models\PerfilChofer::class,
        Models\CategoriaTour::class,
        Models\Tour::class,
        Models\FotoTour::class,
        Models\PuntoRuta::class,
        Models\Vehiculo::class,
        Models\TipoEquipo::class,
        Models\ItemEquipo::class,
        Models\EquipoPorTour::class,
        Models\SalidaTour::class,
        Models\BitacoraViajeVehiculo::class,
        Models\Reserva::class,
        Models\ParticipanteReserva::class,
        Models\AlquilerEquipoReserva::class,
        Models\Boleto::class,
        Models\FichaMedica::class,
        Models\ComprobantePago::class,
        Models\Pago::class,
        Models\Resena::class,
        Models\ConversacionChatbot::class,
        Models\ProyeccionDemanda::class,
        Models\ColaParametroTour::class,
        Models\MarkovTransicion::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (self::MODELOS_AUDITADOS as $modelo) {
            $modelo::observe(AuditObserver::class);
        }
    }
}
