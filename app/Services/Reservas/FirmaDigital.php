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
            throw ValidationException::withMessages([$campo => __('La firma no es válida. Vuelve a firmar.')]);
        }

        $binario = base64_decode($m[1], true);
        if ($binario === false || ! self::pngValido($binario)) {
            throw ValidationException::withMessages([$campo => __('La firma no es válida. Vuelve a firmar.')]);
        }

        $ruta = 'firmas/tmp/'.Str::uuid().'.png';
        if (! Storage::disk(self::DISCO)->put($ruta, $binario)) {
            throw ValidationException::withMessages([$campo => __('No se pudo guardar la firma. Vuelve a intentarlo.')]);
        }

        return $ruta;
    }

    public static function moverDefinitiva(string $rutaTemporal, string $codReserva, int $ptcId): string
    {
        $destino = "firmas/{$codReserva}/{$ptcId}.png";
        $disco = Storage::disk(self::DISCO);

        if (! self::existe($rutaTemporal) || ($rutaTemporal !== $destino && ! $disco->move($rutaTemporal, $destino))) {
            throw ValidationException::withMessages(['firma' => __('La firma no está disponible. Vuelve a firmar.')]);
        }

        return $destino;
    }

    public static function existe(?string $ruta): bool
    {
        $disco = Storage::disk(self::DISCO);

        return $ruta && str_starts_with($ruta, 'firmas/') && ! str_contains($ruta, '..')
            && $disco->exists($ruta) && $disco->size($ruta) <= self::MAX_BYTES
            && self::pngValido($disco->get($ruta));
    }

    private static function pngValido(string $bytes): bool
    {
        if (strlen($bytes) > self::MAX_BYTES || ! str_starts_with($bytes, "\x89PNG\r\n\x1a\n") || ! str_ends_with($bytes, "\x00\x00\x00\x00IEND\xae\x42\x60\x82")) {
            return false;
        }
        $imagen = @getimagesizefromstring($bytes);

        return $imagen && $imagen[2] === IMAGETYPE_PNG && $imagen[0] > 0 && $imagen[1] > 0
            && $imagen[0] <= 4096 && $imagen[1] <= 4096;
    }
}
