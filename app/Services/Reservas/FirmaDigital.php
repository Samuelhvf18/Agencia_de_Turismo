<?php

namespace App\Services\Reservas;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Guarda la firma dibujada en el canvas (PNG en base64) en el disco privado.
 * Primero en firmas/tmp mientras dura el wizard y, al confirmar, en
 * firmas/{codigo de reserva}/.
 */
class FirmaDigital
{
    private const DISCO = 'local';
    private const MAX_BYTES = 300_000;

    /** @return string ruta relativa en el disco privado */
    public static function guardarTemporal(string $dataUri, string $campo): string
    {
        if (! preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $dataUri, $m)) {
            throw ValidationException::withMessages([$campo => 'La firma no es válida. Vuelve a firmar.']);
        }

        $binario = base64_decode($m[1], true);
        if ($binario === false || ! str_starts_with($binario, "\x89PNG\r\n\x1a\n") || strlen($binario) > self::MAX_BYTES) {
            throw ValidationException::withMessages([$campo => 'La firma no es válida. Vuelve a firmar.']);
        }

        $ruta = 'firmas/tmp/'.Str::uuid().'.png';
        Storage::disk(self::DISCO)->put($ruta, $binario);

        return $ruta;
    }

    public static function moverDefinitiva(string $rutaTemporal, string $codReserva, int $ptcId): string
    {
        $destino = "firmas/{$codReserva}/{$ptcId}.png";
        $disco = Storage::disk(self::DISCO);

        if ($rutaTemporal !== $destino && $disco->exists($rutaTemporal)) {
            $disco->move($rutaTemporal, $destino);
        }

        return $destino;
    }

    public static function existe(?string $ruta): bool
    {
        return $ruta && Storage::disk(self::DISCO)->exists($ruta);
    }
}
