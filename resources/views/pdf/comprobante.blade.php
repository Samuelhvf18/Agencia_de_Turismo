{{-- Comprobante de pago (A4). Variables: $comprobante, $reserva, $tour, $salida, $metodos --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $comprobante->cod }}</title>
    <style>
        @page { margin: 36px; }
        body { font-family: DejaVu Sans, sans-serif; color: #343A40; font-size: 11px; }
        .cab { border-bottom: 3px solid #1F5F8B; padding-bottom: 10px; margin-bottom: 16px; }
        .marca { color: #1F5F8B; font-size: 24px; font-weight: bold; }
        .sub { color: #6B6F72; }
        .der { float: right; text-align: right; }
        h2 { font-size: 13px; color: #1F5F8B; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F0F0F1; text-align: left; padding: 6px; font-size: 10px; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .num { text-align: right; }
        .total td { font-weight: bold; font-size: 13px; border-top: 2px solid #1F5F8B; }
        .estado { display: inline-block; padding: 3px 10px; border-radius: 10px; font-weight: bold; }
        .pagado { background: #D1FAE5; color: #065F46; }
        .pendiente { background: #FDF3D7; color: #6B4F3A; }
        .anulado { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
    <div class="cab">
        <div class="der">
            <div><b>Comprobante {{ $comprobante->cod }}</b></div>
            <div class="sub">Emitido el {{ $comprobante->fec_emi->format('d/m/Y') }}</div>
            <div style="margin-top:6px"><span class="estado {{ $comprobante->est }}">{{ strtoupper($comprobante->est) }}</span></div>
        </div>
        <div class="marca">Pipocas</div>
        <div class="sub">Agencia de Viajes y Turismo · La Paz, Bolivia</div>
    </div>

    <h2>Cliente</h2>
    <table>
        <tr><td style="width:30%" class="sub">Nombre</td><td>{{ $reserva->nom_ctc }}</td></tr>
        <tr><td class="sub">Correo</td><td>{{ $reserva->cor_ctc }}</td></tr>
        <tr><td class="sub">Reserva</td><td>{{ $reserva->cod }} · {{ $tour->nom }} · {{ $salida->fec_sal->format('d/m/Y') }} {{ substr($salida->hra_sal, 0, 5) }}</td></tr>
    </table>

    <h2>Detalle</h2>
    <table>
        <thead><tr><th>Concepto</th><th class="num">Cant.</th><th class="num">P. unitario</th><th class="num">Importe</th></tr></thead>
        <tbody>
            <tr>
                <td>Tour {{ $tour->nom }} ({{ $tour->cod }})</td>
                <td class="num">{{ $reserva->cnt_ptc }}</td>
                <td class="num">{{ number_format($tour->pre_bas, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($tour->pre_bas * $reserva->cnt_ptc, 2, ',', '.') }}</td>
            </tr>
            @foreach ($reserva->participantes as $p)
                @foreach ($p->alquileres as $a)
                    <tr>
                        <td>Alquiler: {{ $a->item->tipo->nom }} talla {{ $a->item->tal }} — {{ $p->nom_cpl }}</td>
                        <td class="num">1</td>
                        <td class="num">{{ number_format($a->pre, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format($a->pre, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px; width: 45%; margin-left: 55%;">
        <tr><td class="sub">Subtotal</td><td class="num">Bs. {{ number_format($comprobante->sub, 2, ',', '.') }}</td></tr>
        <tr><td class="sub">IVA (13 %)</td><td class="num">Bs. {{ number_format($comprobante->imp, 2, ',', '.') }}</td></tr>
        <tr class="total"><td>Total</td><td class="num">Bs. {{ number_format($comprobante->mto_tot, 2, ',', '.') }}</td></tr>
    </table>

    <h2>Pagos registrados</h2>
    <table>
        <thead><tr><th>Fecha</th><th>Método</th><th>Referencia</th><th>Estado</th><th class="num">Monto</th></tr></thead>
        <tbody>
            @forelse ($comprobante->pagos->sortBy('pag_en') as $pago)
                <tr>
                    <td>{{ $pago->pag_en->format('d/m/Y H:i') }}</td>
                    <td>{{ $metodos[$pago->mtd] ?? $pago->mtd }}</td>
                    <td>{{ $pago->ref_trx }}</td>
                    <td>{{ ucfirst($pago->est) }}</td>
                    <td class="num">Bs. {{ number_format($pago->mto, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="sub">Sin pagos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="sub" style="margin-top:24px">Documento generado automáticamente por el sistema de Pipocas. Los pagos se procesan mediante una pasarela simulada con fines académicos.</p>
</body>
</html>
