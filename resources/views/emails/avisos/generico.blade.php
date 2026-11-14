<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<body style="margin:0;background:#F8F6F1;font-family:Arial,Helvetica,sans-serif;color:#343A40">
    <div style="max-width:560px;margin:0 auto;padding:24px">
        <div style="background:#1F5F8B;color:#fff;border-radius:16px 16px 0 0;padding:20px 24px">
            <div style="font-size:20px;font-weight:bold">Pipocas</div>
            <div style="opacity:.85">{{ $titulo }}</div>
        </div>
        <div style="background:#fff;border-radius:0 0 16px 16px;padding:24px;font-size:14px;line-height:1.5">
            <p>{{ __('Hola') }} {{ \Illuminate\Support\Str::before($datos['nombre'] ?? 'viajero', ' ') }},</p>

            @if (! empty($datos['mensaje']))
                <p>{{ $datos['mensaje'] }}</p>
            @endif

            @if ($reserva)
                <table style="width:100%;border-collapse:collapse">
                    <tr><td style="padding:4px 0;color:#6B6F72">{{ __('Reserva') }}</td><td style="font-family:monospace;font-weight:bold">{{ $reserva->cod }}</td></tr>
                    <tr><td style="padding:4px 0;color:#6B6F72">Tour</td><td>{{ $reserva->salida->tour->nom }}</td></tr>
                    <tr><td style="padding:4px 0;color:#6B6F72">{{ __('Fecha') }}</td><td>{{ $reserva->salida->fec_sal->format('d/m/Y') }} · {{ substr($reserva->salida->hra_sal, 0, 5) }}</td></tr>
                    <tr><td style="padding:4px 0;color:#6B6F72">{{ __('Estado') }}</td><td>{{ ucfirst($reserva->est) }}</td></tr>
                </table>
                <p style="text-align:center;margin:24px 0">
                    <a href="{{ route('reservas.consultar', ['cod' => $reserva->cod, 'lang' => $reserva->idioma ?? 'es']) }}" style="background:#C96F4A;color:#fff;text-decoration:none;padding:12px 20px;border-radius:10px;font-weight:bold">{{ __('Ver mi reserva') }}</a>
                </p>
            @endif

            @foreach (collect($datos)->except(['nombre', 'destino', 'mensaje', 'reserva', 'error']) as $clave => $valor)
                @if (is_scalar($valor))
                    <p style="margin:2px 0"><span style="color:#6B6F72">{{ ucfirst(str_replace('_', ' ', $clave)) }}:</span> {{ $valor }}</p>
                @endif
            @endforeach

            <p style="font-size:12px;color:#6B6F72;margin-top:24px">{{ __('Mensaje automático de Pipocas Agencia de Viajes y Turismo.') }}</p>
        </div>
    </div>
</body>
</html>
