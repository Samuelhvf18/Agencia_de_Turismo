<?php

namespace App\Services\Reservas;

use Illuminate\Contracts\Session\Session;

/**
 * Borrador del wizard de reserva guardado en la sesión. Nada se escribe en la
 * base hasta confirmar; así un abandono no bloquea cupos ni equipo.
 *
 * Estructura:
 *   tur_id, sal_tur_id, cnt_ptc,
 *   contacto: [nom_ctc, cor_ctc, tel_ctc],
 *   participantes: [i => [nom_cpl, tip_doc, num_doc, ci_cmp, ci_dep_id, eda, pai_id, es_ctc_pri]],
 *   alquileres: [i => [tip_eqp_id => itm_eqp_id]],
 *   fichas: [i => [con_med, nom_ctc_eme, tel_ctc_eme, rta_fir, fir_en, dir_ip]]
 */
class BorradorReserva
{
    private const CLAVE = 'reserva.borrador';

    public function __construct(private Session $session) {}

    public function datos(): array
    {
        return $this->session->get(self::CLAVE, []);
    }

    public function get(string $clave, mixed $defecto = null): mixed
    {
        return data_get($this->datos(), $clave, $defecto);
    }

    public function iniciar(int $turId, int $salTurId, int $cntPtc): void
    {
        $previo = $this->datos();
        $mismoTour = ($previo['tur_id'] ?? null) === $turId;

        $this->session->put(self::CLAVE, [
            'tur_id' => $turId,
            'sal_tur_id' => $salTurId,
            'cnt_ptc' => $cntPtc,
            // Si cambia solo la salida se conservan los datos ya cargados.
            'contacto' => $mismoTour ? ($previo['contacto'] ?? null) : null,
            'participantes' => $mismoTour ? ($previo['participantes'] ?? []) : [],
            'alquileres' => $mismoTour ? ($previo['alquileres'] ?? []) : [],
            'fichas' => $mismoTour ? ($previo['fichas'] ?? []) : [],
        ]);
    }

    public function guardar(string $clave, mixed $valor): void
    {
        $datos = $this->datos();
        data_set($datos, $clave, $valor);
        $this->session->put(self::CLAVE, $datos);
    }

    /** ¿Están completos los pasos previos al indicado? */
    public function tienePaso(string $paso): bool
    {
        $d = $this->datos();

        return match ($paso) {
            'salida' => ! empty($d['sal_tur_id']),
            'participantes' => ! empty($d['participantes']) && ! empty($d['contacto']),
            default => false,
        };
    }

    public function limpiar(): void
    {
        $this->session->forget(self::CLAVE);
    }
}
