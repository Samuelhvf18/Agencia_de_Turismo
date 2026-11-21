<?php

namespace App\Services\Reservas;

use Illuminate\Contracts\Session\Session;

/** Borrador en sesión sin bloqueos. */
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
            // Conserva datos al cambiar salida.
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

    /** Verifica los pasos previos. */
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
