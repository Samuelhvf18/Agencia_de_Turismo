{{-- Boletos con QR (uno por página, A5). Variables: $reserva, $salida, $tour, $items = [['boleto' => Boleto, 'qr' => data URI SVG]] --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Boletos {{ $reserva->cod }}</title>
    <style>
        @page { margin: 22px; }
        body { font-family: DejaVu Sans, sans-serif; color: #343A40; font-size: 11px; }
        .boleto { border: 2px solid #1F5F8B; border-radius: 12px; padding: 18px; page-break-after: always; }
        .boleto:last-child { page-break-after: auto; }
        .marca { color: #1F5F8B; font-size: 20px; font-weight: bold; }
        .sub { color: #6B6F72; font-size: 10px; }
        .tour { font-size: 16px; font-weight: bold; margin: 14px 0 4px; }
        .qr { text-align: center; margin: 16px 0; }
        .qr img { width: 210px; height: 210px; }
        .cod { text-align: center; font-family: DejaVu Sans Mono, monospace; font-size: 15px; font-weight: bold; letter-spacing: 2px; color: #1F5F8B; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 5px 0; border-bottom: 1px dashed #ddd; vertical-align: top; }
        td.k { color: #6B6F72; width: 38%; }
        .aviso { margin-top: 14px; background: #FDF3D7; padding: 8px 10px; border-radius: 8px; font-size: 10px; color: #6B4F3A; }
    </style>
</head>
<body>
@foreach ($items as $item)
    @php($b = $item['boleto'])
    @php($p = $b->participante)
    <div class="boleto">
        <div class="marca">Pipocas</div>
        <div class="sub">Agencia de Viajes y Turismo · La Paz, Bolivia</div>

        <div class="tour">{{ $tour->nom }}</div>
        <div class="sub">{{ $tour->cod }} · Reserva {{ $reserva->cod }}</div>

        <div class="qr"><img src="{{ $item['qr'] }}" alt="QR {{ $b->cod }}"></div>
        <div class="cod">{{ $b->cod }}</div>

        <table>
            <tr><td class="k">Participante</td><td><b>{{ $p->nom_cpl }}</b></td></tr>
            <tr><td class="k">Documento</td><td>{{ $p->tip_doc }} {{ $p->num_doc }}{{ $p->ci_cmp ? '-'.$p->ci_cmp : '' }}</td></tr>
            <tr><td class="k">Fecha y hora</td><td>{{ $salida->fec_sal->locale('es')->translatedFormat('l j \d\e F \d\e Y') }} · {{ substr($salida->hra_sal, 0, 5) }}</td></tr>
            <tr><td class="k">Guía</td><td>{{ $salida->guia?->usuario?->nom ?? 'Por asignar' }}</td></tr>
            <tr><td class="k">Estado</td><td>{{ ucfirst($b->est) }}</td></tr>
        </table>

        <div class="aviso">Presenta este código QR al guía antes de abordar. Llega 15 minutos antes de la hora de salida. El boleto es personal e intransferible.</div>
    </div>
@endforeach
</body>
</html>
