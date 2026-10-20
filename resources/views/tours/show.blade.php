<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $tour->nom }} | Pipocas - Agencia de Viajes y Turismo</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit($tour->des, 155) }}">
<link rel="canonical" href="{{ route('tours.show', $tour) }}">
<meta property="og:title" content="{{ $tour->nom }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit($tour->des, 155) }}">
@if ($tour->img)<meta property="og:image" content="{{ $tour->img }}">@endif
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">
@php $hayRuta = $puntos->count() || ! empty($tour->trz_geo); @endphp
@if ($hayRuta)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
@endif
<style>
:root{
  --principal:#1F5F8B; --secundario:#C96F4A; --acento:#F4C95D; --complementario:#457699;
  --fondo:#F0F0F1; --texto:#343A40; --texto-2:#6B6F72; --calido:#6B4F3A; --hover:#174870;
  --r:22px;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:var(--fondo);color:var(--texto);-webkit-font-smoothing:antialiased;line-height:1.6}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
.wrap{width:100%;max-width:1140px;margin:0 auto;padding:0 clamp(16px,4vw,32px)}
.serif{font-family:'Fraunces',Georgia,serif}

/* navbar */
.top{background:#fff;box-shadow:0 1px 0 rgba(0,0,0,.06)}
.top .wrap{display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;color:var(--principal)}
.logo{width:40px;height:40px;border-radius:12px;background:var(--acento);color:var(--calido);display:grid;place-items:center}
.back{display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:14px;color:var(--principal)}
.back:hover{color:var(--hover)}

/* cabecera */
.hero{position:relative;height:clamp(260px,46vw,460px);overflow:hidden;background:#123a5a}
.hero img{width:100%;height:100%;object-fit:cover}
.hero::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(18,58,90,.1) 30%,rgba(18,58,90,.85))}
.hero .wrap{position:absolute;left:0;right:0;bottom:28px;z-index:1;color:#fff}
.crumbs{font-size:13px;opacity:.85;margin-bottom:10px}
.crumbs a:hover{text-decoration:underline}
.hero h1{font-family:'Fraunces',serif;font-weight:600;font-size:clamp(30px,5vw,52px);line-height:1.08}
.tag{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin-bottom:12px}
.t-bike{background:var(--secundario)}.t-climb{background:var(--principal)}.t-city{background:var(--complementario)}.t-trek{background:var(--hover)}.t-hike{background:var(--calido)}
.code{font-family:ui-monospace,monospace;font-size:12px;opacity:.85;margin-left:8px}

/* contenido */
.grid{display:grid;grid-template-columns:1fr 340px;gap:28px;padding-top:36px;padding-bottom:64px;align-items:start}
.panel{background:#fff;border-radius:var(--r);padding:28px;box-shadow:0 20px 40px -30px rgba(18,58,90,.35)}
.panel+.panel{margin-top:20px}
.panel h2{font-family:'Fraunces',serif;font-weight:600;font-size:22px;margin-bottom:12px;color:var(--principal)}
.panel p{color:var(--texto-2)}
.facts{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.fact{background:#fff;border-radius:16px;padding:16px;text-align:center;box-shadow:0 20px 40px -30px rgba(18,58,90,.35)}
.fact i{color:var(--secundario);font-size:18px}
.fact b{display:block;font-size:18px;margin-top:6px}
.fact small{color:var(--texto-2);font-size:12px}
.bars{display:inline-flex;gap:4px;margin-right:8px;vertical-align:middle}
.bars i{width:22px;height:6px;border-radius:4px;background:#e2e4e6}
.bars i.f{background:var(--secundario)}
.gal{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px}
.gal img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:14px}
#map{height:320px;border-radius:16px;z-index:0}
.perfil{position:relative;height:220px;margin-top:18px}
.pois{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:16px;list-style:none}
.pois li{display:flex;gap:10px;align-items:center;background:var(--fondo);border-radius:12px;padding:10px 12px;font-size:14px}
.pois i{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;color:#fff;font-size:12px;flex-shrink:0}
.pois small{display:block;color:var(--texto-2);font-size:12px}
.poi-inicio{background:#2E7D32}.poi-checkpoint{background:var(--complementario)}.poi-mirador{background:var(--secundario)}.poi-fin{background:var(--principal)}

/* reserva */
.book{position:sticky;top:20px}
.price small{color:var(--texto-2);font-size:13px}
.price b{display:block;font-size:34px;color:var(--principal);line-height:1.1}
.price span{font-size:14px;color:var(--texto-2);font-weight:500}
.book ul{list-style:none;margin:18px 0;display:grid;gap:10px;font-size:14px}
.book li i{color:var(--principal);width:20px}
.btn{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;padding:15px 24px;border-radius:14px;font-weight:700;font-size:15px;background:var(--principal);color:#fff;transition:background .3s}
.btn:hover{background:var(--hover)}

@media(max-width:900px){.grid{grid-template-columns:1fr}.book{position:static}.facts{grid-template-columns:repeat(2,1fr)}}
</style>
</head>
<body>

<header class="top">
  <div class="wrap">
    <a href="{{ route('home') }}" class="brand"><span class="logo"><i class="fa-solid fa-mountain-sun"></i></span> Pipocas</a>
    <a href="{{ route('home') }}#catalogo" class="back"><i class="fa-solid fa-arrow-left"></i> Volver al catálogo</a>
  </div>
</header>

<section class="hero">
  @if ($tour->img)<img src="{{ $tour->img }}" alt="{{ $tour->img_alt }}">@endif
  <div class="wrap">
    <nav class="crumbs" aria-label="Ruta de navegación"><a href="{{ route('home') }}">Inicio</a> / <a href="{{ route('home') }}#catalogo">Tours</a> / {{ $tour->nom }}</nav>
    <span class="tag {{ $tour->tag }}"><i class="{{ $tour->categoria->ico ?: 'fa-solid fa-route' }}"></i> {{ $tour->categoria->nom }}</span><span class="code">{{ $tour->cod }}</span>
    <h1>{{ $tour->nom }}</h1>
  </div>
</section>

<main class="wrap grid">
  <div>
    <div class="facts">
      <div class="fact"><i class="fa-regular fa-clock"></i><b>{{ $tour->dur_txt }} h</b><small>Duración</small></div>
      <div class="fact"><i class="fa-solid fa-mountain"></i><b>{{ $tour->alt_max_m ? number_format($tour->alt_max_m, 0, ',', '.') . ' m' : '—' }}</b><small>Altitud máx.</small></div>
      <div class="fact"><i class="fa-solid fa-users"></i><b>{{ $tour->cap_max }}</b><small>Cupo máximo</small></div>
      <div class="fact"><i class="fa-solid fa-signal"></i><b>{{ $tour->dif_txt }}</b><small>Dificultad</small></div>
    </div>

    <div class="panel">
      <h2>Sobre este tour</h2>
      <p>{{ $tour->des }}</p>
    </div>

    <div class="panel">
      <h2>Dificultad y requisitos</h2>
      <p><span class="bars">@for ($i = 1; $i <= 5; $i++)<i @class(['f' => $i <= $tour->dif_niv])></i>@endfor</span><b>{{ $tour->dif_txt }}</b>
        @if ($tour->alt_min_m && $tour->alt_max_m) · De {{ number_format($tour->alt_min_m, 0, ',', '.') }} a {{ number_format($tour->alt_max_m, 0, ',', '.') }} m s. n. m.@endif</p>
      @if ($tour->req_fis)<p style="margin-top:10px">{{ $tour->req_fis }}</p>@endif
    </div>

    @if ($hayRuta)
    <div class="panel">
      <h2>Recorrido</h2>
      <div id="map" aria-label="Mapa del recorrido"></div>
      <h2 style="margin-top:22px">Perfil de elevación</h2>
      <div class="perfil"><canvas id="perfil" aria-label="Perfil de elevación del recorrido"></canvas></div>
      @if ($puntos->count())
      <ul class="pois">
        @foreach ($puntos as $p)
          <li><i class="poi-{{ $p->tip_pun }} fa-solid {{ ['inicio' => 'fa-flag', 'checkpoint' => 'fa-location-dot', 'mirador' => 'fa-binoculars', 'fin' => 'fa-flag-checkered'][$p->tip_pun] ?? 'fa-location-dot' }}"></i>
            <span>{{ $p->nom }}<small>{{ ucfirst($p->tip_pun) }}@if ($p->alt_m) · {{ number_format($p->alt_m, 0, ',', '.') }} m @endif</small></span></li>
        @endforeach
      </ul>
      @endif
    </div>
    @endif

    <div class="panel">
      <h2>Reseñas verificadas</h2>
      @if ($calificacion && $calificacion->cantidad_resenas > 0)
        <p style="font-size:15px"><b style="font-size:26px;color:var(--principal)">{{ number_format($calificacion->calificacion_promedio, 1, ',', '.') }}</b> / 5
          <span style="color:var(--acento)">@for ($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= round($calificacion->calificacion_promedio) ? 'solid' : 'regular' }} fa-star"></i>@endfor</span>
          · {{ $calificacion->cantidad_resenas }} {{ $calificacion->cantidad_resenas == 1 ? 'reseña' : 'reseñas' }}</p>
        <ul class="pois" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
          @foreach ($resenas as $r)
            <li style="display:block">
              <span style="color:var(--acento)">@for ($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= $r->cal ? 'solid' : 'regular' }} fa-star" style="width:auto;height:auto;background:none;color:inherit;display:inline"></i>@endfor</span>
              @if ($r->com)<p style="margin-top:6px">«{{ $r->com }}»</p>@endif
              <small>{{ $r->mdt['autor'] ?? 'Viajero' }} · <i class="fa-solid fa-circle-check" style="width:auto;height:auto;background:none;color:#2E7D32;display:inline"></i> Reserva verificada · {{ $r->cre_en?->format('m/Y') }}</small>
            </li>
          @endforeach
        </ul>
      @else
        <p style="color:var(--texto-2);font-size:14px">Este tour todavía no tiene reseñas. Solo pueden opinar quienes ya lo realizaron con Pipocas.</p>
      @endif
    </div>

    @if ($tour->galeria->count() > 1)
    <div class="panel">
      <h2>Galería</h2>
      <div class="gal">
        @foreach ($tour->galeria as $foto)
          <img src="{{ $foto['url'] }}" alt="{{ $foto['alt'] }}" loading="lazy">
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <aside class="panel book">
    <div class="price"><small>Desde</small><b>Bs. {{ number_format($tour->pre_bas, 0, ',', '.') }} <span>/ persona</span></b></div>
    <ul>
      <li><i class="fa-solid fa-user-tie"></i> Guía certificado</li>
      <li><i class="fa-solid fa-van-shuttle"></i> Transporte desde La Paz</li>
      <li><i class="fa-solid fa-qrcode"></i> Ticket con código QR</li>
    </ul>
    {{-- Original: <a class="btn" href="{{ Route::has('login') ? route('login') : '#' }}"> --}}
    <a class="btn" href="{{ route('reservas.salida', $tour) }}"><i class="fa-solid fa-calendar-check"></i> Reservar este tour</a>
  </aside>
</main>

@if ($hayRuta)
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
// Trazado: GeoJSON de tours.trz_geo si existe; si no, la polilínea de puntos_ruta.
const puntos = @json($puntos);
const trazado = @json($tour->trz_geo);
const map = L.map('map', { scrollWheelZoom: false });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

const colores = { inicio: '#2E7D32', checkpoint: '#457699', mirador: '#C96F4A', fin: '#1F5F8B' };
puntos.forEach(p => L.circleMarker([+p.lat, +p.lon], { radius: 8, color: '#fff', weight: 2, fillColor: colores[p.tip_pun] || '#457699', fillOpacity: 1 })
  .addTo(map).bindPopup('<b>' + (p.nom || '') + '</b>' + (p.alt_m ? ' · ' + p.alt_m + ' m' : '')));

let capa;
if (trazado) {
  capa = L.geoJSON(trazado, { style: { color: '#C96F4A', weight: 4 } }).addTo(map);
} else if (puntos.length > 1) {
  capa = L.polyline(puntos.map(p => [+p.lat, +p.lon]), { color: '#C96F4A', weight: 4 }).addTo(map);
}
const limites = capa ? capa.getBounds() : L.latLngBounds(puntos.map(p => [+p.lat, +p.lon]));
map.fitBounds(limites.pad(0.2));

// Perfil de elevación (RF-02): distancia acumulada (haversine) contra altitud.
const lineas = [];
const recoger = g => {
  if (!g) return;
  if (g.type === 'FeatureCollection') g.features.forEach(f => recoger(f.geometry));
  else if (g.type === 'Feature') recoger(g.geometry);
  else if (g.type === 'LineString') lineas.push(...g.coordinates);
  else if (g.type === 'MultiLineString') g.coordinates.forEach(l => lineas.push(...l));
};
recoger(trazado);
let serie = lineas.filter(c => c.length > 2).map(c => ({ lat: c[1], lon: c[0], alt: c[2] }));
if (serie.length < 2) serie = puntos.filter(p => p.alt_m !== null).map(p => ({ lat: +p.lat, lon: +p.lon, alt: +p.alt_m, nom: p.nom }));

const rad = x => x * Math.PI / 180;
const km = (a, b) => { const R = 6371, dLat = rad(b.lat - a.lat), dLon = rad(b.lon - a.lon);
  const h = Math.sin(dLat / 2) ** 2 + Math.cos(rad(a.lat)) * Math.cos(rad(b.lat)) * Math.sin(dLon / 2) ** 2; return 2 * R * Math.asin(Math.sqrt(h)); };
let acumulado = 0;
const datos = serie.map((p, i) => { if (i) acumulado += km(serie[i - 1], p); return { x: +acumulado.toFixed(2), y: p.alt }; });

if (datos.length > 1) {
  new Chart(document.getElementById('perfil'), {
    type: 'line',
    data: { datasets: [{ label: 'Altitud (m s. n. m.)', data: datos, parsing: false, fill: true, tension: 0.3, pointRadius: 0,
      borderColor: '#1F5F8B', backgroundColor: 'rgba(31,95,139,.15)' }] },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false },
        tooltip: { callbacks: { title: i => i[0].parsed.x.toFixed(1) + ' km', label: i => i.parsed.y + ' m' } } },
      scales: { x: { type: 'linear', title: { display: true, text: 'Distancia (km)' } }, y: { title: { display: true, text: 'Altitud (m)' } } } },
  });
} else {
  document.querySelector('.perfil').outerHTML = '<p style="color:var(--texto-2);font-size:14px">Este recorrido todavía no tiene altitudes registradas.</p>';
}
</script>
@endif
</body>
</html>
