<!DOCTYPE html>
<html lang="es">
<body style="margin:0;background:#F8F6F1;font-family:Arial,Helvetica,sans-serif;color:#343A40">
    <div style="max-width:560px;margin:0 auto;padding:24px">
        <div style="background:#1F5F8B;color:#fff;border-radius:16px 16px 0 0;padding:24px">
            <div style="font-size:22px;font-weight:bold">Pipocas</div>
            <div style="opacity:.85">¡Tu reserva está confirmada!</div>
        </div>
        <div style="background:#fff;border-radius:0 0 16px 16px;padding:24px">
            <p>Hola {{ \Illuminate\Support\Str::before($reserva->nom_ctc, ' ') }},</p>
            <p>Recibimos tu pago. Estos son los datos de tu salida:</p>
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <tr><td style="padding:6px 0;color:#6B6F72">Código de reserva</td><td style="font-weight:bold;font-family:monospace">{{ $reserva->cod }}</td></tr>
                <tr><td style="padding:6px 0;color:#6B6F72">Tour</td><td>{{ $tour->nom }}</td></tr>
                <tr><td style="padding:6px 0;color:#6B6F72">Fecha</td><td>{{ $salida->fec_sal->format('d/m/Y') }} · {{ substr($salida->hra_sal, 0, 5) }}</td></tr>
                <tr><td style="padding:6px 0;color:#6B6F72">Participantes</td><td>{{ $reserva->cnt_ptc }}</td></tr>
                <tr><td style="padding:6px 0;color:#6B6F72">Total</td><td>Bs. {{ number_format($reserva->pre_tot, 2, ',', '.') }}</td></tr>
            </table>
            <p>Adjuntamos los <b>boletos con código QR</b> (uno por participante) y el <b>comprobante de pago</b>. Presenta el QR al guía antes de abordar.</p>
            <p style="text-align:center;margin:24px 0">
                <a href="{{ route('reservas.consultar', ['cod' => $reserva->cod]) }}" style="background:#C96F4A;color:#fff;text-decoration:none;padding:12px 20px;border-radius:10px;font-weight:bold">Ver mi reserva</a>
            </p>
            <p style="font-size:12px;color:#6B6F72">Si no hiciste esta reserva, ignora este correo.</p>
        </div>
    </div>
</body>
</html>
