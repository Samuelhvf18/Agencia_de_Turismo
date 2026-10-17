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
@if ($puntos->count())
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

    @if ($puntos->count())
    <div class="panel">
      <h2>Recorrido</h2>
      <div id="map" aria-label="Mapa del recorrido"></div>
    </div>
    @endif

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
    <a class="btn" href="{{ Route::has('login') ? route('login') : '#' }}"><i class="fa-solid fa-calendar-check"></i> Reservar este tour</a>
  </aside>
</main>

@if ($puntos->count())
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
const puntos = @json($puntos);
const map = L.map('map', { scrollWheelZoom: false });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
const coords = puntos.map(p => [+p.lat, +p.lon]);
puntos.forEach(p => L.marker([+p.lat, +p.lon]).addTo(map).bindPopup((p.nom || '') + (p.alt_m ? ` · ${p.alt_m} m` : '')));
if (coords.length > 1) L.polyline(coords, { color: '#C96F4A', weight: 4 }).addTo(map);
map.fitBounds(L.latLngBounds(coords).pad(0.2));
</script>
@endif
</body>
</html>
