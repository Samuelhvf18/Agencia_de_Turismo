<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pipocas -Agencia de Viajes y Turismo | Sagárnaga, La Paz</title>
<meta name="description" content="Tours de aventura georreferenciados en La Paz, Bolivia: Biking, Hiking, Trekking, Climbing y City Tours. Reserva en línea y paga con QR.">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">
<style>
:root{
  --principal:#1F5F8B; --secundario:#C96F4A; --acento:#F4C95D; --complementario:#457699;
  --fondo:#F0F0F1; --texto:#343A40; --texto-2:#6B6F72; --calido:#6B4F3A; --hover:#174870;
  --r:22px; --ease:cubic-bezier(.2,.7,.2,1);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:var(--fondo);color:var(--texto);-webkit-font-smoothing:antialiased;line-height:1.55;overflow-x:hidden}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
button{font-family:inherit}
.serif{font-family:'Fraunces',Georgia,serif}
.wrap{width:100%;max-width:1240px;margin:0 auto;padding:0 clamp(16px,4vw,32px)}

/* ---------- animaciones ---------- */
@keyframes up{from{opacity:0;transform:translateY(36px)}to{opacity:1;transform:none}}
@keyframes zoomIn{from{opacity:0;transform:scale(.92)}to{opacity:1;transform:none}}
@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
@keyframes floaty2{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(10px) rotate(-1deg)}}
@keyframes marquee{to{transform:translateX(-50%)}}
@keyframes dash{to{stroke-dashoffset:0}}
@keyframes ping{0%{transform:scale(1);opacity:.7}100%{transform:scale(2.6);opacity:0}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes gradMove{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
.reveal{opacity:0}
.reveal.in{animation:up .9s var(--ease) forwards}
.reveal.zoom.in{animation:zoomIn .9s var(--ease) forwards}
.d1{animation-delay:.08s!important}.d2{animation-delay:.18s!important}.d3{animation-delay:.28s!important}.d4{animation-delay:.38s!important}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}.reveal{opacity:1}}

/* ---------- botones ---------- */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;padding:14px 24px;border-radius:14px;font-weight:700;font-size:15px;border:none;cursor:pointer;transition:transform .25s var(--ease),box-shadow .3s,background .3s;white-space:nowrap}
.btn:hover{transform:translateY(-3px)}
.btn-gold{background:var(--acento);color:var(--calido);box-shadow:0 14px 30px -12px rgba(244,201,93,.9)}
.btn-blue{background:var(--principal);color:#fff;box-shadow:0 14px 30px -12px rgba(31,95,139,.8)}
.btn-blue:hover{background:var(--hover)}
.btn-terra{background:var(--secundario);color:#fff;box-shadow:0 14px 30px -12px rgba(201,111,74,.9)}
.btn-glass{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.35);backdrop-filter:blur(10px)}
.btn-glass:hover{background:rgba(255,255,255,.22)}
.btn .arrow{transition:transform .3s}
.btn:hover .arrow{transform:translateX(4px)}

/* ---------- navbar ---------- */
header{position:fixed;inset:0 0 auto 0;z-index:80;transition:background .35s,box-shadow .35s,height .35s}
header .nav{height:84px;display:flex;align-items:center;justify-content:space-between;gap:16px;transition:height .35s}
header.solid{background:rgba(255,255,255,.92);backdrop-filter:blur(16px);box-shadow:0 8px 30px -18px rgba(23,72,112,.5)}
header.solid .nav{height:68px}
.brand{display:flex;align-items:center;gap:11px}
.logo{height:44px;width:44px;border-radius:14px;display:grid;place-items:center;color:var(--calido);font-size:19px;background:var(--acento);box-shadow:0 10px 22px -10px rgba(244,201,93,.9);flex-shrink:0}
.brand b{font-size:23px;font-weight:800;letter-spacing:-.03em;color:#fff;transition:color .35s}
.brand b i{font-style:normal;color:var(--acento)}
.brand small{white-space:nowrap;display:block;font-size:11px;color:rgba(255,255,255,.75);font-weight:500;transition:color .35s;line-height:1.1}
header.solid .brand b{color:var(--principal)}
header.solid .brand b i{color:var(--secundario)}
header.solid .brand small{color:var(--texto-2)}
.links{display:flex;gap:6px;align-items:center}
.links a{white-space:nowrap;padding:9px 14px;border-radius:11px;font-size:14px;font-weight:600;color:rgba(255,255,255,.88);transition:background .25s,color .25s}
.links a:hover{background:rgba(255,255,255,.14);color:#fff}
header.solid .links a{color:var(--texto-2)}
header.solid .links a:hover{background:rgba(31,95,139,.08);color:var(--principal)}
.nav-cta{display:flex;gap:8px;align-items:center}
.nav-cta .login{font-weight:700;font-size:14px;color:#fff;padding:10px 14px;transition:color .3s}
header.solid .nav-cta .login{color:var(--principal)}
.nav-cta .btn{padding:11px 18px;font-size:14px}
.burger{display:none;height:44px;width:44px;border-radius:12px;border:1px solid rgba(255,255,255,.35);background:rgba(255,255,255,.1);color:#fff;font-size:18px;cursor:pointer}
header.solid .burger{border-color:#dcdce0;background:#fff;color:var(--principal)}
.mobile-menu{position:fixed;inset:0;z-index:90;background:var(--hover);color:#fff;padding:24px clamp(16px,4vw,32px);display:flex;flex-direction:column;transform:translateY(-100%);transition:transform .5s var(--ease)}
.mobile-menu.open{transform:none}
.mobile-menu .mm-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:40px}
.mobile-menu .mm-top button{height:44px;width:44px;border-radius:12px;border:1px solid rgba(255,255,255,.3);background:transparent;color:#fff;font-size:20px}
.mobile-menu nav a{display:flex;justify-content:space-between;align-items:center;font-family:'Fraunces',serif;font-size:30px;padding:14px 0;border-bottom:1px solid rgba(255,255,255,.12)}
.mobile-menu nav a i{font-size:16px;color:var(--acento)}
.mobile-menu .mm-btns{margin-top:auto;display:grid;gap:12px}
@media(max-width:1140px){.links,.nav-cta .login,.nav-cta .btn{display:none}.burger{display:grid;place-items:center}}

/* ---------- hero ---------- */
.hero{position:relative;min-height:100vh;min-height:100svh;display:flex;align-items:center;color:#fff;overflow:hidden;padding:130px 0 170px;background:linear-gradient(140deg,#123a5a 0%,var(--hover) 35%,var(--principal) 70%,var(--complementario) 100%);background-size:200% 200%;animation:gradMove 18s ease infinite}
.hero::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 80% 20%,rgba(244,201,93,.28),transparent 40%),radial-gradient(circle at 10% 90%,rgba(201,111,74,.35),transparent 45%)}
.sun{position:absolute;top:14%;right:8%;height:140px;width:140px;border-radius:50%;background:radial-gradient(circle,var(--acento) 0%,rgba(244,201,93,.5) 45%,transparent 70%);filter:blur(2px);animation:floaty 9s ease-in-out infinite;opacity:.8}
.hero-grid{position:relative;z-index:3;display:grid;grid-template-columns:1.05fr .95fr;gap:56px;align-items:center}
.pill{display:inline-flex;align-items:center;gap:10px;padding:8px 16px 8px 10px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.22);backdrop-filter:blur(8px);font-size:13px;font-weight:600;margin-bottom:26px}
.pill .live{position:relative;height:9px;width:9px;border-radius:50%;background:var(--acento)}
.pill .live::after{content:"";position:absolute;inset:0;border-radius:50%;background:var(--acento);animation:ping 1.6s infinite}
.hero h1{font-family:'Fraunces',serif;font-weight:600;font-size:clamp(40px,6.4vw,80px);line-height:1;letter-spacing:-.03em;margin-bottom:24px}
.hero h1 em{font-style:italic;background:linear-gradient(90deg,var(--acento),#f7d98b,var(--secundario));-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p.lead{font-size:clamp(16px,1.6vw,19px);color:#d7e4ee;max-width:540px;margin-bottom:34px}
.hero-ctas{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:38px}
.search{display:flex;align-items:center;gap:6px;background:#fff;border-radius:18px;padding:8px;max-width:560px;box-shadow:0 30px 60px -25px rgba(0,0,0,.55)}
.search .f{flex:1;display:flex;align-items:center;gap:10px;padding:8px 12px;min-width:0}
.search .f i{color:var(--secundario)}
.search .f + .f{border-left:1px solid #ececef}
.search label{display:block;font-size:11px;font-weight:700;color:var(--texto-2);text-transform:uppercase;letter-spacing:.06em}
.search select,.search input{border:none;outline:none;background:transparent;font:600 14px 'Plus Jakarta Sans';color:var(--texto);width:100%}
.search .btn{padding:14px 18px;border-radius:13px}
.trust{display:flex;align-items:center;gap:14px;margin-top:28px;font-size:13px;color:#cfdde8}
.avatars{display:flex}
.avatars img{height:36px;width:36px;border-radius:50%;border:2px solid var(--hover);object-fit:cover;margin-left:-10px}
.avatars img:first-child{margin-left:0}
.trust b{color:#fff}
.trust .stars{color:var(--acento);letter-spacing:2px}

.collage{position:relative;height:560px}
.collage .ph{position:absolute;border-radius:28px;overflow:hidden;box-shadow:0 40px 70px -30px rgba(0,0,0,.7);border:4px solid rgba(255,255,255,.15)}
.collage .ph img{width:100%;height:100%;object-fit:cover;transition:transform 1.2s var(--ease)}
.collage .ph:hover img{transform:scale(1.08)}
.ph.a{top:0;right:0;width:68%;height:64%;animation:floaty 8s ease-in-out infinite}
.ph.b{bottom:0;left:0;width:52%;height:52%;animation:floaty2 9s ease-in-out infinite}
.ph.c{bottom:6%;right:4%;width:38%;height:34%;animation:floaty 7s 1s ease-in-out infinite}
.badge{position:absolute;z-index:4;background:#fff;color:var(--texto);border-radius:16px;padding:12px 16px;display:flex;align-items:center;gap:11px;box-shadow:0 24px 40px -18px rgba(0,0,0,.6);font-size:13px;font-weight:700}
.badge small{display:block;font-weight:500;color:var(--texto-2);font-size:11.5px}
.badge .bi{height:38px;width:38px;border-radius:11px;display:grid;place-items:center;color:#fff;flex-shrink:0}
.badge.b1{top:46%;left:-6%;animation:floaty 6s ease-in-out infinite}
.badge.b2{top:6%;left:6%;animation:floaty2 7s ease-in-out infinite}
.mountains{position:absolute;left:0;right:0;bottom:-1px;z-index:2;line-height:0}
.mountains svg{width:100%;height:auto;display:block}
@media(max-width:1024px){
  .hero-grid{grid-template-columns:1fr;gap:40px}
  .collage{height:420px;max-width:620px;width:100%;margin:0 auto}
  .badge.b1{left:0}
}
@media(max-width:600px){
  .hero{padding:110px 0 120px}
  .collage{height:320px}
  .ph.c{display:none}
  .ph.a{width:74%;height:66%}
  .ph.b{width:56%;height:50%}
  .badge{padding:9px 12px;font-size:12px}
  .badge .bi{height:32px;width:32px}
  .badge.b2{display:none}
  .badge.b1{top:auto;bottom:4%;left:auto;right:0}
  .search{flex-direction:column;align-items:stretch;padding:10px}
  .search .f + .f{border-left:none;border-top:1px solid #ececef}
  .search .btn{width:100%}
  .hero-ctas .btn{flex:1}
  .sun{height:90px;width:90px}
}

/* ---------- marquee ---------- */
.marquee{background:var(--fondo);overflow:hidden;padding:26px 0;border-bottom:1px solid #e2e2e5}
.marquee .track{display:flex;gap:46px;width:max-content;animation:marquee 32s linear infinite}
.marquee span{display:flex;align-items:center;gap:14px;font-family:'Fraunces',serif;font-size:clamp(20px,3vw,30px);color:var(--principal);white-space:nowrap}
.marquee span i{color:var(--secundario);font-size:.7em}
.marquee:hover .track{animation-play-state:paused}

/* ---------- secciones base ---------- */
section.s{padding:clamp(64px,9vw,110px) 0}
.eyebrow{display:inline-flex;align-items:center;gap:10px;color:var(--secundario);font-weight:800;font-size:12.5px;letter-spacing:.16em;text-transform:uppercase}
.eyebrow::before{content:"";height:2px;width:26px;background:var(--secundario);border-radius:2px}
.h2{font-family:'Fraunces',serif;font-weight:600;font-size:clamp(32px,4.6vw,54px);line-height:1.05;letter-spacing:-.02em;color:var(--principal);margin-top:12px}
.h2 em{font-style:italic;color:var(--secundario)}
.sub{color:var(--texto-2);font-size:clamp(15px,1.4vw,17px);max-width:560px;margin-top:14px}
.head-row{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:46px}

/* ---------- stats ---------- */
.stats{display:grid;grid-template-columns:repeat(4,1fr);background:#fff;border-radius:26px;box-shadow:0 30px 60px -40px rgba(23,72,112,.5);margin-top:-60px;position:relative;z-index:5;overflow:hidden}
.stat{padding:30px 26px;border-right:1px solid #eee;position:relative}
.stat:last-child{border-right:none}
.stat .ic{height:44px;width:44px;border-radius:13px;display:grid;place-items:center;margin-bottom:14px;font-size:18px}
.stat .n{font-size:clamp(28px,3.4vw,40px);font-weight:800;color:var(--principal);line-height:1;letter-spacing:-.02em}
.stat .l{font-size:13px;color:var(--texto-2);margin-top:6px;font-weight:600}
@media(max-width:860px){.stats{grid-template-columns:repeat(2,1fr)}.stat:nth-child(2){border-right:none}.stat:nth-child(-n+2){border-bottom:1px solid #eee}}
@media(max-width:420px){.stat{padding:22px 16px}}

/* ---------- categorías bento ---------- */
.bento{display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:230px 230px;gap:18px}
.tile{position:relative;border-radius:var(--r);overflow:hidden;color:#fff;cursor:pointer;isolation:isolate}
.tile img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:-2;transition:transform 1s var(--ease)}
.tile::after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,transparent 30%,rgba(23,72,112,.92) 100%);transition:opacity .4s}
.tile:hover img{transform:scale(1.1)}
.tile .tin{position:absolute;left:22px;right:22px;bottom:20px;display:flex;justify-content:space-between;align-items:flex-end;gap:10px}
.tile h3{font-family:'Fraunces',serif;font-size:26px;font-weight:600;line-height:1}
.tile p{font-size:13px;color:#d7e4ee;margin-top:5px}
.tile .go{height:44px;width:44px;flex-shrink:0;border-radius:50%;background:var(--acento);color:var(--calido);display:grid;place-items:center;transform:rotate(-45deg) scale(.8);opacity:0;transition:all .4s var(--ease)}
.tile:hover .go{transform:none;opacity:1}
.tile .count{position:absolute;top:18px;left:18px;font-size:12px;font-weight:700;padding:6px 11px;border-radius:999px;background:rgba(255,255,255,.18);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25)}
.tile.big{grid-column:span 2;grid-row:span 2}
.tile.big h3{font-size:clamp(30px,3.4vw,42px)}
.tile.wide{grid-column:span 2}
@media(max-width:960px){.bento{grid-template-columns:repeat(2,1fr);grid-template-rows:auto}.tile{height:220px}.tile.big{grid-column:span 2;grid-row:auto;height:320px}.tile.wide{grid-column:span 2}}
@media(max-width:560px){.bento{grid-template-columns:1fr}.tile,.tile.big,.tile.wide{grid-column:auto;height:210px}.tile .go{opacity:1;transform:none}}

/* ---------- catálogo ---------- */
.filters{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding-bottom:4px;max-width:100%}
.filters::-webkit-scrollbar{display:none}
.chip{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:999px;border:1px solid #d8d8dc;background:#fff;color:var(--texto-2);font-weight:700;font-size:13.5px;cursor:pointer;transition:all .25s}
.chip:hover{border-color:var(--principal);color:var(--principal)}
.chip.on{background:var(--principal);border-color:var(--principal);color:#fff;box-shadow:0 10px 20px -10px rgba(31,95,139,.8)}
.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:26px}
@media(max-width:1024px){.cards{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){.cards{grid-template-columns:1fr}.head-row .filters{width:100%}}
.card{background:#fff;border-radius:var(--r);overflow:hidden;display:flex;flex-direction:column;box-shadow:0 2px 0 #e7e7ea,0 20px 40px -34px rgba(23,72,112,.6);transition:transform .45s var(--ease),box-shadow .45s}
.card:hover{transform:translateY(-10px);box-shadow:0 2px 0 #e7e7ea,0 40px 60px -30px rgba(23,72,112,.55)}
.card.hide{display:none}
.cimg{position:relative;height:230px;overflow:hidden;margin:10px 10px 0;border-radius:16px}
.cimg img{width:100%;height:100%;object-fit:cover;transition:transform .9s var(--ease)}
.card:hover .cimg img{transform:scale(1.1)}
.tag{position:absolute;top:12px;left:12px;padding:6px 12px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:#fff;display:flex;align-items:center;gap:6px}
.fav{position:absolute;top:10px;right:10px;height:38px;width:38px;border-radius:50%;background:rgba(255,255,255,.9);color:var(--secundario);display:grid;place-items:center;border:none;cursor:pointer;transition:transform .25s}
.fav:hover{transform:scale(1.15)}
.fav.on i{font-weight:900}
.code{position:absolute;bottom:12px;left:12px;font-size:11px;font-weight:600;color:#fff;background:rgba(23,72,112,.75);backdrop-filter:blur(6px);padding:5px 10px;border-radius:8px;font-family:ui-monospace,monospace}
.t-bike{background:var(--secundario)}.t-climb{background:var(--principal)}.t-city{background:var(--complementario)}.t-trek{background:var(--hover)}.t-hike{background:var(--calido)}
.cbody{padding:20px 22px 22px;display:flex;flex-direction:column;flex:1}
.meta{display:flex;gap:16px;font-size:12.5px;color:var(--texto-2);font-weight:600;margin-bottom:10px;flex-wrap:wrap}
.meta i{color:var(--complementario);margin-right:4px}
.card h3{font-family:'Fraunces',serif;font-size:21px;font-weight:600;color:var(--texto);line-height:1.2;margin-bottom:8px}
.card p{font-size:14px;color:var(--texto-2);margin-bottom:18px}
.diff{display:flex;align-items:center;gap:10px;font-size:12px;font-weight:700;color:var(--calido);margin-bottom:18px}
.bars{display:flex;gap:4px}
.bars i{height:6px;width:22px;border-radius:4px;background:#e6e6ea}
.bars i.f{background:var(--secundario)}
.cfoot{margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px dashed #e3e3e6}
.price small{display:block;font-size:11.5px;color:var(--texto-2)}
.price b{font-size:22px;font-weight:800;color:var(--principal)}
.price b span{font-size:12px;font-weight:500;color:var(--texto-2)}
.cbtn{height:46px;padding:0 18px;border-radius:13px;background:var(--fondo);color:var(--principal);font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:all .3s}
.card:hover .cbtn{background:var(--principal);color:#fff}

/* ---------- cómo funciona ---------- */
.how{background:#fff}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;position:relative;margin-top:56px}
.steps::before{content:"";position:absolute;top:34px;left:12%;right:12%;height:2px;background:repeating-linear-gradient(90deg,var(--complementario) 0 10px,transparent 10px 20px);opacity:.5}
.step{text-align:center;position:relative}
.step .num{height:68px;width:68px;margin:0 auto 20px;border-radius:22px;display:grid;place-items:center;font-size:24px;color:#fff;position:relative;transition:transform .4s var(--ease)}
.step:hover .num{transform:translateY(-6px) rotate(-6deg)}
.step .num b{position:absolute;top:-8px;right:-8px;height:26px;width:26px;border-radius:50%;background:var(--acento);color:var(--calido);font-size:12px;display:grid;place-items:center;border:3px solid #fff}
.step h4{font-size:17px;font-weight:800;color:var(--texto);margin-bottom:6px}
.step p{font-size:14px;color:var(--texto-2);max-width:240px;margin:0 auto}
@media(max-width:860px){.steps{grid-template-columns:repeat(2,1fr);row-gap:40px}.steps::before{display:none}}
@media(max-width:480px){.steps{grid-template-columns:1fr}}

/* ---------- mapa georreferenciado ---------- */
.geo{background:var(--hover);color:#fff;position:relative;overflow:hidden}
.geo::before{content:"";position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.08) 1px,transparent 1px);background-size:22px 22px}
.geo-grid{position:relative;display:grid;grid-template-columns:1fr 1.1fr;gap:56px;align-items:center}
.geo .h2{color:#fff}
.geo .h2 em{color:var(--acento)}
.geo .sub{color:#c8d8e6}
.geo ul{list-style:none;margin:30px 0 34px;display:grid;gap:14px}
.geo li{display:flex;gap:14px;align-items:flex-start;font-size:15px;color:#dbe6ef}
.geo li i{height:34px;width:34px;flex-shrink:0;border-radius:10px;background:rgba(244,201,93,.15);color:var(--acento);display:grid;place-items:center;font-size:14px}
.mapbox{position:relative;background:#0f3352;border-radius:28px;padding:16px;border:1px solid rgba(255,255,255,.1);box-shadow:0 40px 80px -40px rgba(0,0,0,.8)}
.mapbox svg{width:100%;height:auto;display:block;border-radius:18px}
.route{stroke-dasharray:900;stroke-dashoffset:900}
.mapbox.in .route{animation:dash 3.2s var(--ease) forwards}
.pin{position:absolute;transform:translate(-50%,-100%);display:flex;flex-direction:column;align-items:center}
.pin span{background:#fff;color:var(--texto);font-size:11.5px;font-weight:700;padding:5px 10px;border-radius:9px;white-space:nowrap;box-shadow:0 10px 20px -10px rgba(0,0,0,.6);margin-bottom:6px}
.pin i{height:14px;width:14px;border-radius:50%;background:var(--acento);border:3px solid #fff;position:relative}
.pin i::after{content:"";position:absolute;inset:-3px;border-radius:50%;background:var(--acento);animation:ping 1.8s infinite}
.map-stats{position:absolute;right:28px;bottom:28px;background:rgba(255,255,255,.95);color:var(--texto);border-radius:14px;padding:12px 16px;display:flex;gap:18px;font-size:12px;box-shadow:0 20px 40px -20px rgba(0,0,0,.7)}
.map-stats b{display:block;font-size:17px;color:var(--principal)}
@media(max-width:960px){.geo-grid{grid-template-columns:1fr}}
@media(max-width:480px){.map-stats{position:static;margin-top:12px;justify-content:space-between;gap:8px}.pin span{font-size:10px;padding:4px 7px}}


/* ---------- verificar ---------- */
.verify-box{background:linear-gradient(120deg,var(--principal),var(--complementario));border-radius:32px;padding:clamp(32px,5vw,64px);color:#fff;display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;position:relative;overflow:hidden}
.verify-box::after{content:"";position:absolute;right:-80px;top:-80px;height:280px;width:280px;border-radius:50%;border:40px solid rgba(244,201,93,.18)}
.verify-box .h2{color:#fff;font-size:clamp(28px,3.6vw,42px)}
.verify-box .sub{color:#d6e3ee}
.verify-box code{background:rgba(255,255,255,.15);padding:2px 8px;border-radius:6px;color:var(--acento);font-weight:700}
.vform{position:relative;z-index:2;background:#fff;border-radius:20px;padding:10px;display:flex;gap:8px;box-shadow:0 30px 50px -25px rgba(0,0,0,.6)}
.vform input{flex:1;min-width:0;border:none;outline:none;padding:14px 16px;font:700 16px ui-monospace,monospace;letter-spacing:.1em;text-transform:uppercase;color:var(--texto)}
.vform .btn{padding:14px 20px}
.vnote{position:relative;z-index:2;display:flex;gap:18px;margin-top:16px;font-size:13px;color:#d6e3ee;flex-wrap:wrap}
.vnote i{color:var(--acento);margin-right:5px}
@media(max-width:860px){.verify-box{grid-template-columns:1fr}}
@media(max-width:480px){.vform{flex-direction:column}.vform input{text-align:center}}

/* ---------- confianza / legal ---------- */
.legal-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.lcard{background:#fff;border-radius:20px;padding:24px;display:flex;gap:16px;align-items:flex-start;border:1px solid transparent;transition:border-color .3s,transform .3s var(--ease)}
.lcard:hover{border-color:var(--complementario);transform:translateY(-4px)}
.lcard .li{height:48px;width:48px;flex-shrink:0;border-radius:14px;display:grid;place-items:center;font-size:19px}
.lcard h4{font-size:16px;font-weight:800;margin-bottom:4px}
.lcard p{font-size:13.5px;color:var(--texto-2)}
.lcard a{display:inline-flex;gap:6px;align-items:center;font-size:13px;font-weight:700;color:var(--secundario);margin-top:10px}
@media(max-width:960px){.legal-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.legal-grid{grid-template-columns:1fr}}

/* ---------- CTA final ---------- */
.cta{background:var(--acento);border-radius:32px;padding:clamp(36px,6vw,72px);display:flex;justify-content:space-between;align-items:center;gap:30px;flex-wrap:wrap;position:relative;overflow:hidden}
.cta::before{content:"";position:absolute;left:-60px;bottom:-120px;height:300px;width:300px;border-radius:50%;background:rgba(201,111,74,.18)}
.cta h2{font-family:'Fraunces',serif;font-size:clamp(30px,4.4vw,52px);line-height:1.05;color:var(--calido);max-width:620px;position:relative}
.cta h2 em{color:var(--principal)}
.cta .btns{display:flex;gap:12px;flex-wrap:wrap;position:relative}
@media(max-width:600px){.cta .btns,.cta .btns .btn{width:100%}}

/* ---------- footer ---------- */
footer{background:#0f3352;color:#fff;padding:80px 0 30px;margin-top:clamp(64px,9vw,110px);position:relative}
.fgrid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1.2fr;gap:40px;margin-bottom:50px}
footer h5{font-size:12.5px;letter-spacing:.14em;text-transform:uppercase;color:var(--acento);margin-bottom:18px}
footer .fl a{display:block;color:#b4c7d8;font-size:14.5px;margin-bottom:11px;transition:color .25s,transform .25s}
footer .fl a:hover{color:#fff;transform:translateX(4px)}
.fbrand p{color:#b4c7d8;font-size:14.5px;margin:18px 0 22px;max-width:300px}
.soc{display:flex;gap:10px}
.soc a{height:42px;width:42px;border-radius:12px;background:rgba(255,255,255,.08);display:grid;place-items:center;transition:background .3s,transform .3s}
.soc a:hover{background:var(--secundario);transform:translateY(-3px)}
.news{display:flex;background:rgba(255,255,255,.08);border-radius:14px;padding:6px;margin-top:6px}
.news input{flex:1;min-width:0;background:transparent;border:none;outline:none;color:#fff;padding:10px 12px;font:500 14px 'Plus Jakarta Sans'}
.news input::placeholder{color:#8fa8be}
.news button{border:none;border-radius:10px;background:var(--acento);color:var(--calido);padding:0 16px;font-weight:800;cursor:pointer}
.consent{display:flex;gap:9px;align-items:flex-start;font-size:12px;color:#9fb6ca;margin-top:12px}
.consent input{margin-top:3px;accent-color:var(--acento)}
.fbottom{border-top:1px solid rgba(255,255,255,.1);padding-top:24px;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;font-size:13px;color:#8fa8be}
.fbottom a:hover{color:#fff}
@media(max-width:960px){.fgrid{grid-template-columns:1fr 1fr}}
@media(max-width:520px){.fgrid{grid-template-columns:1fr}.fbottom{flex-direction:column}}

/* ---------- cookies ---------- */
.cookie{position:fixed;left:50%;bottom:20px;z-index:70;transform:translate(-50%,140%);width:min(720px,calc(100% - 32px));background:#fff;border-radius:20px;padding:18px 20px;box-shadow:0 30px 70px -20px rgba(15,51,82,.55);display:flex;gap:16px;align-items:center;transition:transform .7s var(--ease)}
.cookie.show{transform:translate(-50%,0)}
.cookie .ci{height:46px;width:46px;flex-shrink:0;border-radius:14px;background:var(--acento);color:var(--calido);display:grid;place-items:center;font-size:20px}
.cookie .ct{flex:1;font-size:13px;color:var(--texto-2)}
.cookie .ct b{display:block;color:var(--texto);font-size:14.5px}
.cookie .ct a{color:var(--principal);font-weight:700;text-decoration:underline}
.cookie .ca{display:flex;gap:8px}
.cookie .ca button{padding:11px 16px;border-radius:12px;font-weight:700;font-size:13px;cursor:pointer;border:1px solid #dcdce0;background:#fff;color:var(--texto)}
.cookie .ca .acc{background:var(--principal);color:#fff;border-color:var(--principal)}
@media(max-width:640px){.cookie{flex-direction:column;align-items:stretch;text-align:left}.cookie .ci{display:none}.cookie .ca{display:grid;grid-template-columns:1fr 1fr 1fr}}

/* ---------- back to top ---------- */
.totop{position:fixed;right:20px;bottom:20px;z-index:60;height:50px;width:50px;border-radius:16px;border:none;background:var(--secundario);color:#fff;font-size:18px;cursor:pointer;box-shadow:0 16px 30px -12px rgba(201,111,74,.9);opacity:0;transform:translateY(20px);transition:all .4s var(--ease);pointer-events:none}
.totop.show{opacity:1;transform:none;pointer-events:auto}
.cookie.show ~ .totop{bottom:130px}
@media(max-width:640px){.cookie.show ~ .totop{bottom:230px}}
/* ---------- modales legales ---------- */
.lmodal{border:none;padding:0;margin:auto;width:min(680px,calc(100% - 32px));max-height:min(86vh,820px);border-radius:var(--r);background:#fff;color:var(--texto);box-shadow:0 40px 80px -30px rgba(18,58,90,.55);overflow:hidden}
.lmodal[open]{display:flex;flex-direction:column;animation:zoomIn .35s var(--ease)}
.lmodal::backdrop{background:rgba(18,58,90,.55);backdrop-filter:blur(4px)}
.lm-head{display:flex;gap:16px;align-items:center;padding:22px 24px;border-bottom:1px solid #ececef}
.lm-head .li{height:48px;width:48px;flex-shrink:0;border-radius:14px;display:grid;place-items:center;font-size:19px}
.lm-head h3{font-family:'Fraunces',serif;font-weight:600;font-size:clamp(20px,3vw,24px);line-height:1.15}
.lm-head small{display:block;margin-top:2px;font-size:12.5px;line-height:1.4;color:var(--texto-2)}
.lm-x{margin-left:auto;align-self:flex-start;height:38px;width:38px;flex-shrink:0;border-radius:12px;border:1px solid #dcdce0;background:#fff;color:var(--texto);cursor:pointer;font-size:16px;transition:background .2s,color .2s}
.lm-x:hover{background:var(--principal);border-color:var(--principal);color:#fff}
.lm-body{padding:22px 24px;overflow-y:auto;font-size:14.5px;line-height:1.65}
.lm-body p{margin-bottom:12px}
.lm-body h5{font-size:15px;font-weight:800;color:var(--principal);margin:20px 0 8px}
.lm-body ul,.lm-body ol{padding-left:20px;margin-bottom:12px}
.lm-body li{margin-bottom:6px}
.lm-body li::marker{color:var(--secundario)}
.lm-laws li{margin-bottom:10px}
.lm-table{width:100%;border-collapse:collapse;margin:6px 0 14px;font-size:14px}
.lm-table th,.lm-table td{text-align:left;padding:10px 12px;border-bottom:1px solid #ececef}
.lm-table th{background:var(--fondo);font-weight:700;font-size:13px}
.lm-table td:last-child{white-space:nowrap}
.lm-foot{padding:16px 24px;border-top:1px solid #ececef;display:flex;justify-content:flex-end}
.lm-ok{padding:12px 22px;border-radius:12px;border:none;background:var(--principal);color:#fff;font-weight:700;font-size:14px;cursor:pointer;transition:background .2s}
.lm-ok:hover{background:var(--hover)}
@media(max-width:640px){.lmodal{width:100%;max-height:92vh;margin:auto 0 0;border-radius:var(--r) var(--r) 0 0}.lm-head,.lm-body{padding-left:18px;padding-right:18px}.lm-foot{padding:14px 18px}.lm-ok{width:100%}}
</style>
</head>
<body>




<!-- NAVBAR  -->
<header id="hdr">
  <div class="wrap nav">
    <a href="{{ url('/') }}" class="brand">
      <span class="logo"><i class="fa-solid fa-mountain-sun"></i></span>
      <span><b>Agencia de Turismo<i>.</i></b><small>Todo es con todos similado</small></span>
    </a>
    <nav class="links">
      <a href="#categorias">Experiencias</a>
      <a href="#catalogo">Tours</a>
      <a href="#como">Cómo reservar</a>
      <a href="#verificar">Mi reserva</a>
      <a href="{{ url('/chatbot') }}"><i class="fa-solid fa-robot"></i> Guia Digital</a>
    </nav>
    <div class="nav-cta">
      @auth
        <a href="{{ url('/dashboard') }}" class="btn btn-gold"><i class="fa-solid fa-gauge"></i> Panel</a>
      @else
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="login">Iniciar sesión</a>
        @endif
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="btn btn-gold">Registrarse</a>
        @endif
      @endauth
      <button class="burger" id="openMenu" aria-label="Abrir menú"><i class="fa-solid fa-bars-staggered"></i></button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="menu">
  <div class="mm-top">
    <a href="{{ url('/') }}" class="brand"><span class="logo"><i class="fa-solid fa-mountain-sun"></i></span><span><b>Pipocas<i>.</i></b></span></a>
    <button id="closeMenu" aria-label="Cerrar menú"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <nav>
    <a href="#categorias">Experiencias <i class="fa-solid fa-arrow-right"></i></a>
    <a href="#catalogo">Tours <i class="fa-solid fa-arrow-right"></i></a>
    <a href="#como">Cómo reservar <i class="fa-solid fa-arrow-right"></i></a>
    <a href="#verificar">Mi reserva <i class="fa-solid fa-arrow-right"></i></a>
    <a href="{{ url('/chatbot') }}">Guia Digital: <i class="fa-solid fa-robot"></i></a>
  </nav>
  <div class="mm-btns">
    @auth
      <a href="{{ url('/dashboard') }}" class="btn btn-gold"><i class="fa-solid fa-gauge"></i> Ir a mi panel</a>
    @else
      @if (Route::has('login'))
        <a href="{{ route('login') }}" class="btn btn-glass">Iniciar sesión</a>
      @endif
      @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-gold">Registrarse</a>
      @endif
    @endauth
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <div class="sun"></div>
  <div class="wrap hero-grid">
    <div>
      <span class="pill reveal"><span class="live"></span> Salidas diarias desde La Paz, Bolivia</span>
      <h1 class="reveal d1">Conecta<br>con la aventura <em>en el corazón de Bolivia</em></h1>
      <p class="lead reveal d2">Rutas georreferenciadas de Biking, Hiking, Trekking y Climbing. Reserva en línea, paga con QR y sal con equipo certificado y guías expertos.</p>
      <div class="hero-ctas reveal d3">
        <a href="#catalogo" class="btn btn-gold"><i class="fa-solid fa-compass"></i> Explorar tours <i class="fa-solid fa-arrow-right arrow"></i></a>
        <a href="#verificar" class="btn btn-glass"><i class="fa-solid fa-qrcode"></i> Mi reserva</a>
      </div>
      <form class="search reveal d4" onsubmit="event.preventDefault();location.hash='catalogo'">
        <div class="f"><i class="fa-solid fa-person-hiking"></i><div style="flex:1;min-width:0"><label>Actividad</label>
          <select><option>Todas</option><option>Biking</option><option>Hiking</option><option>Trekking</option><option>Climbing</option><option>City Tour</option></select></div></div>
        <div class="f"><i class="fa-regular fa-calendar"></i><div style="flex:1;min-width:0"><label>Fecha</label><input type="date"></div></div>
        <button class="btn btn-blue" aria-label="Buscar"><i class="fa-solid fa-magnifying-glass"></i> <span>Buscar</span></button>
      </form>

    </div>

    <div class="collage reveal zoom d2">
      <div class="ph a"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=900&auto=format&fit=crop" alt="Huayna Potosí"></div>
      <div class="ph b"><img src="https://images.unsplash.com/photo-1544198365-f5d60b6d8190?q=80&w=700&auto=format&fit=crop" alt="Camino de la Muerte"></div>
      <div class="ph c"><img src="https://images.unsplash.com/photo-1518638150340-f706e86654de?q=80&w=600&auto=format&fit=crop" alt="Valle de la Luna"></div>
      <div class="badge b1"><span class="bi" style="background:var(--secundario)"><i class="fa-solid fa-shield-heart"></i></span><span>Reserva segura<small>Pago QR · Ficha médica</small></span></div>
      <div class="badge b2"><span class="bi" style="background:var(--principal)"><i class="fa-solid fa-mountain"></i></span><span>5.100 m<small>Huayna Potosí</small></span></div>
    </div>
  </div>

  <div class="mountains">
    <svg viewBox="0 0 1440 220" preserveAspectRatio="none">
      <path fill="#457699" fill-opacity=".55" d="M0 150 L120 90 L230 140 L360 50 L470 120 L590 70 L720 140 L860 40 L990 120 L1120 60 L1250 130 L1360 80 L1440 110 L1440 220 L0 220Z"/>
      <path fill="#C96F4A" fill-opacity=".55" d="M0 180 L150 120 L280 170 L420 100 L560 165 L700 115 L840 175 L980 105 L1130 170 L1270 120 L1440 160 L1440 220 L0 220Z"/>
      <path fill="#F0F0F1" d="M0 205 L180 165 L340 200 L520 160 L700 198 L880 162 L1060 200 L1240 168 L1440 196 L1440 220 L0 220Z"/>
    </svg>
  </div>
</section>

<!-- ================= MARQUEE ================= -->
<div class="marquee" aria-hidden="true">
  <div class="track">
    <span>Biking <i class="fa-solid fa-asterisk"></i></span><span>Hiking <i class="fa-solid fa-asterisk"></i></span><span>Trekking <i class="fa-solid fa-asterisk"></i></span><span>Climbing <i class="fa-solid fa-asterisk"></i></span><span>City Tour <i class="fa-solid fa-asterisk"></i></span><span>Cordillera Real <i class="fa-solid fa-asterisk"></i></span><span>Yungas <i class="fa-solid fa-asterisk"></i></span><span>Tiwanaku <i class="fa-solid fa-asterisk"></i></span>
    <span>Biking <i class="fa-solid fa-asterisk"></i></span><span>Hiking <i class="fa-solid fa-asterisk"></i></span><span>Trekking <i class="fa-solid fa-asterisk"></i></span><span>Climbing <i class="fa-solid fa-asterisk"></i></span><span>City Tour <i class="fa-solid fa-asterisk"></i></span><span>Cordillera Real <i class="fa-solid fa-asterisk"></i></span><span>Yungas <i class="fa-solid fa-asterisk"></i></span><span>Tiwanaku <i class="fa-solid fa-asterisk"></i></span>
  </div>
</div>

<!-- ================= STATS ================= -->
<div class="wrap" style="margin-top:80px">
  <div class="stats reveal">
    <div class="stat"><div class="ic" style="background:rgba(31,95,139,.1);color:var(--principal)"><i class="fa-solid fa-route"></i></div><div class="n" data-count="15" data-suf="+">0</div><div class="l">Rutas georreferenciadas</div></div>
    <div class="stat"><div class="ic" style="background:rgba(201,111,74,.12);color:var(--secundario)"><i class="fa-solid fa-mountain"></i></div><div class="n" data-count="5100" data-suf=" m">0</div><div class="l">Altitud máxima</div></div>
    <div class="stat"><div class="ic" style="background:rgba(244,201,93,.25);color:var(--calido)"><i class="fa-solid fa-helmet-safety"></i></div><div class="n" data-count="100" data-suf="%">0</div><div class="l">Equipo certificado</div></div>
    <div class="stat"><div class="ic" style="background:rgba(69,118,153,.12);color:var(--complementario)"><i class="fa-solid fa-robot"></i></div><div class="n">24/7</div><div class="l">Asistencia con IA</div></div>
  </div>
</div>

<!-- ================= CATEGORÍAS ================= -->
<section class="s" id="categorias">
  <div class="wrap">
    <div class="head-row">
      <div class="reveal">
        <span class="eyebrow">Experiencias</span>
        <h2 class="h2">Elige cómo quieres<br><em>vivir la montaña</em></h2>
      </div>
      <p class="sub reveal d1">Desde paseos culturales hasta glaciares a más de 5.000 metros. Cada ruta está trazada en mapa y revisada por nuestros guías.</p>
    </div>
    <div class="bento">
      <a class="tile big reveal zoom" data-go="climbing"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1200&auto=format&fit=crop" alt=""><span class="count">3 rutas</span><div class="tin"><div><h3>Climbing</h3><p>Glaciares y cumbres de la Cordillera Real</p></div><span class="go"><i class="fa-solid fa-arrow-right"></i></span></div></a>
      <a class="tile reveal zoom d1" data-go="biking"><img src="https://images.unsplash.com/photo-1544198365-f5d60b6d8190?q=80&w=700&auto=format&fit=crop" alt=""><span class="count">2 rutas</span><div class="tin"><div><h3>Biking</h3><p>Descensos de adrenalina</p></div><span class="go"><i class="fa-solid fa-arrow-right"></i></span></div></a>
      <a class="tile reveal zoom d2" data-go="hiking"><img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=700&auto=format&fit=crop" alt=""><span class="count">4 rutas</span><div class="tin"><div><h3>Hiking</h3><p>Caminos prehispánicos</p></div><span class="go"><i class="fa-solid fa-arrow-right"></i></span></div></a>
      <a class="tile wide reveal zoom d3" data-go="trekking"><img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1100&auto=format&fit=crop" alt=""><span class="count">3 rutas</span><div class="tin"><div><h3>Trekking</h3><p>Lagunas glaciares y picos nevados</p></div><span class="go"><i class="fa-solid fa-arrow-right"></i></span></div></a>
    </div>
  </div>
</section>

<!-- ================= CATÁLOGO ================= -->
<section class="s" id="catalogo" style="padding-top:0">
  <div class="wrap">
    <div class="head-row">
      <div class="reveal">
        <span class="eyebrow">Catálogo oficial</span>
        <h2 class="h2">Tours <em>destacados</em></h2>
      </div>
      <div class="filters reveal d1" id="filters">
        <button class="chip on" data-f="todos">Todos</button>
        <button class="chip" data-f="biking"><i class="fa-solid fa-person-biking"></i> Biking</button>
        <button class="chip" data-f="hiking"><i class="fa-solid fa-person-hiking"></i> Hiking</button>
        <button class="chip" data-f="trekking"><i class="fa-solid fa-mountain-sun"></i> Trekking</button>
        <button class="chip" data-f="climbing"><i class="fa-solid fa-mountain"></i> Climbing</button>
        <button class="chip" data-f="city"><i class="fa-solid fa-landmark"></i> City Tour</button>
      </div>
    </div>

    <div class="cards" id="cards">
      <article class="card reveal" data-cat="biking">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1544198365-f5d60b6d8190?q=80&w=800&auto=format&fit=crop" alt="Camino de la Muerte"><span class="tag t-bike"><i class="fa-solid fa-person-biking"></i> Biking</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ01</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>7.5 h</span><span><i class="fa-solid fa-mountain"></i>4.700 m</span><span><i class="fa-solid fa-users"></i>Máx 12</span></div>
          <h3>Descenso Camino de la Muerte</h3><p>De La Cumbre a Coroico en bicicleta de doble suspensión con guías expertos.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i class="f"></i><i class="f"></i><i class="f"></i><i></i></span>Difícil</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 350 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      <article class="card reveal d1" data-cat="climbing">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800&auto=format&fit=crop" alt="Huayna Potosí"><span class="tag t-climb"><i class="fa-solid fa-mountain"></i> Climbing</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ02</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>8 h</span><span><i class="fa-solid fa-mountain"></i>5.100 m</span><span><i class="fa-solid fa-users"></i>Máx 6</span></div>
          <h3>Glaciar Huayna Potosí</h3><p>Iniciación al montañismo glaciar con crampones, piolet y guía de alta montaña.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i class="f"></i><i class="f"></i><i class="f"></i><i class="f"></i></span>Experto</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 480 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      <article class="card reveal d2" data-cat="city">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1518638150340-f706e86654de?q=80&w=800&auto=format&fit=crop" alt="Valle de la Luna"><span class="tag t-city"><i class="fa-solid fa-landmark"></i> City Tour</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ03</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>4 h</span><span><i class="fa-solid fa-mountain"></i>3.600 m</span><span><i class="fa-solid fa-users"></i>Máx 20</span></div>
          <h3>Valle de la Luna &amp; Teleféricos</h3><p>Formaciones lunares y vista panorámica desde el teleférico urbano más alto del mundo.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i></i><i></i><i></i><i></i></span>Fácil</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 180 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      <article class="card reveal" data-cat="trekking">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop" alt="Condoriri"><span class="tag t-trek"><i class="fa-solid fa-mountain-sun"></i> Trekking</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ04</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>6.5 h</span><span><i class="fa-solid fa-mountain"></i>4.500 m</span><span><i class="fa-solid fa-users"></i>Máx 10</span></div>
          <h3>Laguna Chiar Khota &amp; Condoriri</h3><p>Caminata de altura entre lagunas turquesa y picos nevados de la Cordillera.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i class="f"></i><i class="f"></i><i></i><i></i></span>Moderado</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 260 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      <article class="card reveal d1" data-cat="hiking">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=800&auto=format&fit=crop" alt="El Choro"><span class="tag t-hike"><i class="fa-solid fa-person-hiking"></i> Hiking</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ05</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>7 h</span><span><i class="fa-solid fa-mountain"></i>4.200 m</span><span><i class="fa-solid fa-users"></i>Máx 12</span></div>
          <h3>Camino Prehispánico El Choro</h3><p>Empedrados incas originales de Apacheta a Challapampa con vista al valle subtropical.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i class="f"></i><i class="f"></i><i></i><i></i></span>Moderado</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 290 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      <article class="card reveal d2" data-cat="city">
        <div class="cimg"><img src="https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?q=80&w=800&auto=format&fit=crop" alt="Tiwanaku"><span class="tag t-city"><i class="fa-solid fa-landmark"></i> City Tour</span><button class="fav" aria-label="Favorito"><i class="fa-regular fa-heart"></i></button><span class="code">TUR-2026-LPZ06</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>6 h</span><span><i class="fa-solid fa-mountain"></i>3.850 m</span><span><i class="fa-solid fa-users"></i>Máx 20</span></div>
          <h3>Tiwanaku &amp; Puma Punku</h3><p>La cuna de la civilización andina con guía arqueológico y la Puerta del Sol.</p>
          <div class="diff"><span class="bars"><i class="f"></i><i></i><i></i><i></i><i></i></span>Fácil</div>
          <div class="cfoot"><div class="price"><small>Desde</small><b>Bs. 220 <span>/ pers</span></b></div><a class="cbtn" href="#">Reservar <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
    </div>
  </div>
</section>

<!-- ================= CÓMO RESERVAR ================= -->
<section class="s how" id="como">
  <div class="wrap" style="text-align:center">
    <span class="eyebrow reveal">Cómo reservar</span>
    <h2 class="h2 reveal d1">Tu aventura en <em>4 pasos</em></h2>
    <div class="steps">
      <div class="step reveal"><div class="num" style="background:var(--principal)"><i class="fa-solid fa-map-location-dot"></i><b>1</b></div><h4>Elige tu ruta</h4><p>Explora el mapa y compara dificultad, altitud y duración.</p></div>
      <div class="step reveal d1"><div class="num" style="background:var(--complementario)"><i class="fa-solid fa-file-medical"></i><b>2</b></div><h4>Completa tus datos</h4><p>Solo lo necesario, más la ficha médica en rutas de altura.</p></div>
      <div class="step reveal d2"><div class="num" style="background:var(--secundario)"><i class="fa-solid fa-qrcode"></i><b>3</b></div><h4>Paga con QR</h4><p>Pago rápido y seguro, confirmación al instante.</p></div>
      <div class="step reveal d3"><div class="num" style="background:var(--calido)"><i class="fa-solid fa-ticket"></i><b>4</b></div><h4>Recibe tu ticket</h4><p>Código único y QR para el check‑in con tu guía.</p></div>
    </div>
  </div>
</section>

<!-- ================= MAPA GEO ================= -->
<section class="s geo">
  <div class="wrap geo-grid">
    <div>
      <span class="eyebrow reveal" style="color:var(--acento)">Rutas georreferenciadas</span>
      <h2 class="h2 reveal d1">Cada paso,<br><em>trazado en el mapa</em></h2>
      <p class="sub reveal d2">Conoce el recorrido exacto antes de salir: puntos de encuentro, altitudes y paradas, con seguimiento del grupo durante el tour.</p>
      <ul>
        <li class="reveal d2"><i class="fa-solid fa-location-crosshairs"></i>Punto de encuentro y paradas marcados en el mapa</li>
        <li class="reveal d3"><i class="fa-solid fa-chart-area"></i>Perfil de altitud y distancia de cada ruta</li>
        <li class="reveal d4"><i class="fa-solid fa-satellite-dish"></i>Guía con check‑in y monitoreo en tiempo real</li>
      </ul>
      <a href="#catalogo" class="btn btn-gold reveal d4">Ver rutas en el mapa <i class="fa-solid fa-arrow-right arrow"></i></a>
    </div>
    <div class="mapbox reveal zoom d2" id="mapbox">
      <div style="position:relative">
      <svg viewBox="0 0 600 440">
        <rect width="600" height="440" fill="#174870"/>
        <g fill="none" stroke="#457699" stroke-opacity=".45" stroke-width="1.2">
          <path d="M-20 360 C 100 300, 160 380, 300 320 S 520 260, 640 300"/>
          <path d="M-20 300 C 90 250, 180 320, 300 260 S 520 200, 640 240"/>
          <path d="M-20 240 C 120 190, 200 260, 320 200 S 520 140, 640 180"/>
          <path d="M-20 180 C 120 130, 220 200, 340 140 S 520 90, 640 120"/>
          <path d="M-20 120 C 120 70, 220 140, 340 80 S 520 40, 640 60"/>
          <path d="M100 440 C 140 380, 120 320, 180 260"/>
          <path d="M420 440 C 400 380, 460 320, 430 250"/>
        </g>
        <path d="M60 380 L 120 300 L 170 330 L 240 230 L 300 270 L 380 160 L 450 200 L 530 80" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
        <path class="route" d="M60 380 L 120 300 L 170 330 L 240 230 L 300 270 L 380 160 L 450 200 L 530 80" fill="none" stroke="#F4C95D" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="900"/>
        <g fill="#C96F4A"><circle cx="120" cy="300" r="6"/><circle cx="240" cy="230" r="6"/><circle cx="380" cy="160" r="6"/></g>
      </svg>
      <div class="pin" style="left:10%;top:87%"><span>Sagárnaga</span><i></i></div>
      <div class="pin" style="left:63%;top:37.5%"><span>Campo base 4.700 m</span><i></i></div>
      <div class="pin" style="left:88%;top:19.5%"><span>Cumbre 5.100 m</span><i></i></div>
      </div>
      <div class="map-stats"><div><b>32 km</b>Distancia</div><div><b>+1.450 m</b>Desnivel</div><div><b>8 h</b>Duración</div></div>
    </div>
  </div>
</section>

<!-- ================= VERIFICAR ================= -->
<section class="s" id="verificar" style="padding-top:0">
  <div class="wrap">
    <div class="verify-box reveal">
      <div>
        <span class="eyebrow" style="color:var(--acento)">Mi reserva</span>
        <h2 class="h2">Consulta tu reserva al instante</h2>
        <p class="sub">Ingresa tu código único (ej. <code>RES-88A9F2</code>) para ver el estado de pago, la salida y tu ticket QR.</p>
      </div>
      <div>
        <form class="vform" onsubmit="event.preventDefault();this.querySelector('button').innerHTML='<i class=\'fa-solid fa-circle-check\'></i> Confirmada'">
          <input type="text" placeholder="RES-XXXXXX" required>
          <button class="btn btn-terra"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
        </form>
        <div class="vnote"><span><i class="fa-solid fa-lock"></i>Sin necesidad de cuenta</span><span><i class="fa-solid fa-qrcode"></i>Ticket QR descargable</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ================= CONFIANZA / LEGAL ================= -->
<section class="s" style="padding-top:0">
  <div class="wrap">
    <div class="head-row">
      <div class="reveal"><span class="eyebrow">Transparencia</span><h2 class="h2">Reserva con <em>total confianza</em></h2></div>
      <p class="sub reveal d1">Tus datos y tu compra están protegidos. Consulta nuestras políticas en cualquier momento.</p>
    </div>
    <div class="legal-grid">
      <div class="lcard reveal"><span class="li" style="background:rgba(31,95,139,.1);color:var(--principal)"><i class="fa-solid fa-file-contract"></i></span><div><h4>Términos y Condiciones</h4><p>Reglas de reserva, salidas y responsabilidades.</p><a href="#m-terminos" data-modal="m-terminos">Leer <i class="fa-solid fa-arrow-right"></i></a></div></div>
      <div class="lcard reveal d1"><span class="li" style="background:rgba(69,118,153,.12);color:var(--complementario)"><i class="fa-solid fa-user-shield"></i></span><div><h4>Política de Privacidad</h4><p>Solo pedimos los datos necesarios para tu tour.</p><a href="#m-privacidad" data-modal="m-privacidad">Leer <i class="fa-solid fa-arrow-right"></i></a></div></div>
      <div class="lcard reveal d2"><span class="li" style="background:rgba(244,201,93,.25);color:var(--calido)"><i class="fa-solid fa-cookie-bite"></i></span><div><h4>Política de Cookies</h4><p>Tú decides qué cookies no esenciales aceptar.</p><a href="#m-cookies" data-modal="m-cookies">Leer <i class="fa-solid fa-arrow-right"></i></a></div></div>
      <div class="lcard reveal"><span class="li" style="background:rgba(201,111,74,.12);color:var(--secundario)"><i class="fa-solid fa-rotate-left"></i></span><div><h4>Política de Reembolso</h4><p>Cancelaciones y devoluciones claras.</p><a href="#m-reembolso" data-modal="m-reembolso">Leer <i class="fa-solid fa-arrow-right"></i></a></div></div>
      <div class="lcard reveal d1"><span class="li" style="background:rgba(23,72,112,.1);color:var(--hover)"><i class="fa-solid fa-square-check"></i></span><div><h4>Consentimiento registrado</h4><p>Dos casillas separadas y registro de cuándo aceptaste.</p><a href="#m-consentimiento" data-modal="m-consentimiento">Saber más <i class="fa-solid fa-arrow-right"></i></a></div></div>
      <div class="lcard reveal d2"><span class="li" style="background:rgba(107,79,58,.12);color:var(--calido)"><i class="fa-solid fa-scale-balanced"></i></span><div><h4>Normativa boliviana</h4><p>Cumplimiento de la legislación local vigente.</p><a href="#m-normativa" data-modal="m-normativa">Saber más <i class="fa-solid fa-arrow-right"></i></a></div></div>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<div class="wrap">
  <div class="cta reveal zoom">
    <h2>¿Listo para tu próxima <em>aventura andina?</em></h2>
    <div class="btns">
      <a href="#catalogo" class="btn btn-blue"><i class="fa-solid fa-compass"></i> Reservar ahora</a>
      <a href="#" class="btn btn-terra"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
    </div>
  </div>
</div>

<!-- ================= FOOTER ================= -->
<footer>
  <div class="wrap">
    <div class="fgrid">
      <div class="fbrand">
        <a href="{{ url('/') }}" class="brand"><span class="logo"><i class="fa-solid fa-mountain-sun"></i></span><span><b>Pipocas<i>.</i></b></span></a>
        <p>Agencia de viajes y turismo de aventura en el corazón de La Paz, Bolivia.</p>
        <div class="soc"><a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a><a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a></div>
      </div>
      <div class="fl"><h5>Tours</h5><a href="#">Biking</a><a href="#">Hiking</a><a href="#">Trekking</a><a href="#">Climbing</a><a href="#">City Tour</a></div>
      <div class="fl"><h5>Legal</h5><a href="#">Términos y condiciones</a><a href="#">Privacidad</a><a href="#">Cookies</a><a href="#">Reembolsos</a></div>
      <div>
        <h5>Novedades</h5>
        <form class="news" onsubmit="event.preventDefault()"><input type="email" placeholder="Tu correo"><button>Unirme</button></form>
        <label class="consent"><input type="checkbox"> Acepto recibir novedades y he leído la Política de Privacidad.</label>
      </div>
    </div>
    <div class="fbottom">
      <span>Calle Sagárnaga #234, Zona Central · La Paz, Bolivia</span>
      <span>© {{ date('Y') }} Pipocas. Todos los derechos reservados.</span>
    </div>
  </div>
</footer>

<!-- ================= COOKIES ================= -->
<!-- ================= MODALES LEGALES ================= -->
<dialog class="lmodal" id="m-terminos" aria-labelledby="m-terminos-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(31,95,139,.1);color:var(--principal)"><i class="fa-solid fa-file-contract"></i></span>
    <div><h3 id="m-terminos-t">Términos y Condiciones</h3><small>Última actualización: octubre de 2026</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>Estos términos regulan la contratación de tours y servicios de <b>Pipocas, Agencia de Viajes y Turismo</b>, con oficina en Calle Sagárnaga #234, Zona Central, La Paz, Bolivia. Al confirmar una reserva declaras haberlos leído y aceptado.</p>
    <h5>1. Reservas y cupos</h5>
    <ul>
      <li>La reserva queda confirmada cuando se registra el pago (total o el anticipo indicado en el tour) y recibes tu ticket con código QR por correo.</li>
      <li>Cada salida tiene un cupo máximo y un mínimo de participantes. Si no se alcanza el mínimo, te ofreceremos otra fecha, otro tour o la devolución total.</li>
      <li>Debes registrar a cada participante con nombre completo, documento de identidad o pasaporte, nacionalidad y un contacto de emergencia.</li>
    </ul>
    <h5>2. Precios y pagos</h5>
    <ul>
      <li>Los precios están expresados en bolivianos (Bs) e incluyen lo detallado en la ficha del tour. Lo no indicado (comidas extra, propinas, entradas a áreas protegidas cuando así se señale) corre por cuenta del turista.</li>
      <li>Aceptamos pago con QR, tarjeta y transferencia. Emitimos factura conforme a la normativa del Servicio de Impuestos Nacionales (SIN).</li>
    </ul>
    <h5>3. Salidas</h5>
    <ul>
      <li>Preséntate en el punto de encuentro 15 minutos antes de la hora indicada. Pasado el tiempo de tolerancia, la salida parte y se considera no presentación.</li>
      <li>Por seguridad, podemos modificar el itinerario, reprogramar o suspender una salida por clima, bloqueos, conflictos sociales o disposiciones de las autoridades. En esos casos aplica lo previsto en la Política de Reembolso.</li>
    </ul>
    <h5>4. Salud, seguridad y equipo</h5>
    <ul>
      <li>Los tours de aventura (biking, trekking, climbing) exigen completar y firmar digitalmente la ficha médica y el deslinde de responsabilidad antes de la salida.</li>
      <li>La Paz está a más de 3.600 m s. n. m. Recomendamos al menos dos días de aclimatación antes de rutas de alta montaña. El guía puede impedir que continúe quien presente signos de mal de altura o ponga en riesgo al grupo.</li>
      <li>El equipo alquilado debe devolverse en el estado en que se entregó. Los daños por mal uso o la pérdida se cobran según la tarifa vigente.</li>
    </ul>
    <h5>5. Responsabilidades</h5>
    <p>Pipocas responde por la correcta prestación de los servicios contratados, con guías y vehículos habilitados. No respondemos por pérdidas de objetos personales no confiados a la agencia ni por daños derivados de no seguir las indicaciones del guía. Cualquier reclamo puede presentarse en nuestra oficina o ante las instancias de defensa del consumidor previstas por la Ley N.º 453.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<dialog class="lmodal" id="m-privacidad" aria-labelledby="m-privacidad-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(69,118,153,.12);color:var(--complementario)"><i class="fa-solid fa-user-shield"></i></span>
    <div><h3 id="m-privacidad-t">Política de Privacidad</h3><small>Última actualización: octubre de 2026</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>En Pipocas aplicamos el principio de <b>mínimo dato</b>: solo pedimos lo necesario para organizar tu tour de forma segura y cumplir nuestras obligaciones legales.</p>
    <h5>Qué datos recogemos</h5>
    <ul>
      <li><b>Identificación:</b> nombre, documento de identidad o pasaporte, nacionalidad y fecha de nacimiento.</li>
      <li><b>Contacto:</b> correo, teléfono o WhatsApp y un contacto de emergencia.</li>
      <li><b>Salud:</b> la ficha médica de los tours de aventura (alergias, condiciones previas, medicación). Es un dato sensible y solo lo ven el guía y el coordinador de tu salida.</li>
      <li><b>Pago y facturación:</b> NIT o CI para la factura. Los datos de tarjeta los procesa directamente la pasarela de pago; nosotros no los almacenamos.</li>
    </ul>
    <h5>Para qué los usamos</h5>
    <ul>
      <li>Gestionar tu reserva, emitir tu ticket QR y tu factura.</li>
      <li>Asignar guía, chofer y vehículo, y actuar ante una emergencia durante el tour.</li>
      <li>Enviarte avisos sobre tu salida. Las novedades y promociones solo si lo autorizas aparte.</li>
      <li>Elaborar estadísticas anónimas (rutas más solicitadas, nacionalidades) para mejorar el servicio.</li>
    </ul>
    <h5>Con quién los compartimos</h5>
    <p>Solo con quienes intervienen en tu tour (guías, transportistas, administradores de áreas protegidas cuando exigen registro de visitantes), con la pasarela de pago y con autoridades que lo requieran por ley. No vendemos ni cedemos tus datos.</p>
    <h5>Conservación y seguridad</h5>
    <p>Guardamos los datos de la reserva mientras dure la relación y el tiempo exigido por la normativa tributaria. La ficha médica se elimina pasados 6 meses del tour. Usamos conexión cifrada, acceso por roles y una bitácora que registra quién consulta o modifica la información.</p>
    <h5>Tus derechos</h5>
    <p>Conforme al derecho a la privacidad e intimidad (art. 21 y 130 de la Constitución Política del Estado) y a la Ley N.º 164 y su reglamento (D.S. N.º 1793), puedes pedir acceso, corrección o eliminación de tus datos y retirar tu consentimiento escribiéndonos o visitando nuestra oficina.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<dialog class="lmodal" id="m-cookies" aria-labelledby="m-cookies-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(244,201,93,.25);color:var(--calido)"><i class="fa-solid fa-cookie-bite"></i></span>
    <div><h3 id="m-cookies-t">Política de Cookies</h3><small>Última actualización: octubre de 2026</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>Las cookies son pequeños archivos que el sitio guarda en tu navegador. Las dividimos en dos grupos y <b>tú decides</b> sobre las que no son imprescindibles.</p>
    <h5>Cookies esenciales (siempre activas)</h5>
    <ul>
      <li><b>Sesión:</b> mantienen tu inicio de sesión y tu carrito mientras reservas.</li>
      <li><b>Seguridad:</b> el token CSRF que protege los formularios frente a envíos falsos.</li>
      <li><b>Preferencias de cookies:</b> recuerdan lo que elegiste en el banner para no volver a preguntarte.</li>
    </ul>
    <h5>Cookies no esenciales (solo con tu permiso)</h5>
    <ul>
      <li><b>Analítica:</b> cuentan visitas y páginas vistas de forma agregada para saber qué tours interesan más.</li>
      <li><b>Mapas y contenido de terceros:</b> algunos mapas o videos incrustados pueden instalar cookies propias del proveedor.</li>
    </ul>
    <h5>Cómo gestionarlas</h5>
    <p>Al entrar verás un banner con tres opciones: <b>Aceptar</b> todas, <b>Rechazar</b> las no esenciales o <b>Configurar</b> cada categoría. Puedes cambiar tu decisión cuando quieras borrando las cookies del navegador o desde el enlace de cookies al pie del sitio. Rechazarlas no te impide reservar.</p>
    <p>No usamos cookies para publicidad personalizada ni compartimos la información con redes publicitarias.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<dialog class="lmodal" id="m-reembolso" aria-labelledby="m-reembolso-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(201,111,74,.12);color:var(--secundario)"><i class="fa-solid fa-rotate-left"></i></span>
    <div><h3 id="m-reembolso-t">Política de Reembolso</h3><small>Última actualización: octubre de 2026</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>Queremos que cancelar sea tan claro como reservar. El porcentaje devuelto depende de con cuánta anticipación nos avisas, contada desde la hora de salida del tour.</p>
    <table class="lm-table">
      <thead><tr><th>Aviso de cancelación</th><th>Devolución</th></tr></thead>
      <tbody>
        <tr><td>72 horas o más</td><td><b>100 %</b></td></tr>
        <tr><td>Entre 48 y 72 horas</td><td><b>80 %</b></td></tr>
        <tr><td>Entre 24 y 48 horas</td><td><b>50 %</b></td></tr>
        <tr><td>Menos de 24 horas o no presentación</td><td>Sin devolución</td></tr>
      </tbody>
    </table>
    <h5>Cancelación por parte de Pipocas</h5>
    <p>Si suspendemos la salida por clima, seguridad, bloqueos o por no alcanzar el mínimo de participantes, eliges entre <b>reprogramar sin costo</b> o recibir la <b>devolución del 100 %</b>.</p>
    <h5>Cambios de fecha</h5>
    <p>Puedes cambiar la fecha una vez sin costo si avisas con al menos 48 horas, sujeto a cupo disponible.</p>
    <h5>Cómo se devuelve</h5>
    <ul>
      <li>Solicítalo desde tu cuenta, por WhatsApp o en nuestra oficina, indicando el código de tu ticket.</li>
      <li>Devolvemos por el mismo medio de pago en un plazo de hasta 10 días hábiles. Las comisiones bancarias o de la pasarela que no sean reembolsables pueden descontarse.</li>
      <li>El alquiler de equipo se devuelve íntegro si el tour no se realizó. Los servicios ya consumidos (por ejemplo, entradas compradas a tu nombre) no son reembolsables.</li>
    </ul>
    <p>Esta política no limita los derechos que te reconoce la Ley N.º 453 de los Derechos de las Usuarias y los Usuarios y de las Consumidoras y los Consumidores.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<dialog class="lmodal" id="m-consentimiento" aria-labelledby="m-consentimiento-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(23,72,112,.1);color:var(--hover)"><i class="fa-solid fa-square-check"></i></span>
    <div><h3 id="m-consentimiento-t">Consentimiento registrado</h3><small>Cómo dejamos constancia de lo que aceptas</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>Al registrarte o reservar no encontrarás casillas premarcadas ni un único “acepto todo”. Te pedimos <b>dos consentimientos separados</b>, cada uno con su propia casilla:</p>
    <ol>
      <li><b>Términos y Condiciones y Política de Reembolso.</b> Obligatoria para reservar, porque define las reglas del servicio que contratas.</li>
      <li><b>Tratamiento de datos personales</b>, incluida la ficha médica en los tours de aventura. Explica qué datos usamos y para qué, según la Política de Privacidad.</li>
    </ol>
    <p>Recibir novedades y promociones es una tercera opción, siempre voluntaria; no marcarla no afecta tu reserva.</p>
    <h5>Qué registramos</h5>
    <ul>
      <li>Qué documento aceptaste y en qué versión.</li>
      <li>Fecha y hora exactas de la aceptación.</li>
      <li>La dirección IP y el navegador desde el que lo hiciste.</li>
      <li>La firma digital de la ficha médica, cuando corresponde.</li>
    </ul>
    <p>Este registro queda en la bitácora de auditoría del sistema y no puede editarse. Nos sirve para demostrar, ante ti o ante una autoridad, que la aceptación fue libre, informada y expresa, en línea con la Ley N.º 164 y el D.S. N.º 1793 sobre documentos y firmas digitales.</p>
    <h5>Retirar tu consentimiento</h5>
    <p>Puedes retirar el consentimiento de datos o de promociones cuando quieras. El retiro se registra igual que la aceptación y no afecta lo ya realizado; si anula un dato imprescindible para un tour pendiente, te ofreceremos las opciones de la Política de Reembolso.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<dialog class="lmodal" id="m-normativa" aria-labelledby="m-normativa-t">
  <div class="lm-head">
    <span class="li" style="background:rgba(107,79,58,.12);color:var(--calido)"><i class="fa-solid fa-scale-balanced"></i></span>
    <div><h3 id="m-normativa-t">Normativa boliviana</h3><small>Marco legal que cumplimos</small></div>
    <button class="lm-x" data-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="lm-body">
    <p>Pipocas opera como agencia de viajes y turismo en La Paz, Bolivia, y ajusta sus servicios a la legislación nacional y municipal vigente:</p>
    <ul class="lm-laws">
      <li><b>Ley N.º 292, Ley General de Turismo “Bolivia te espera”.</b> Regula a los prestadores de servicios turísticos. Estamos inscritos en el registro de prestadores del Viceministerio de Turismo y trabajamos con guías habilitados.</li>
      <li><b>Ley N.º 453, de los Derechos de las Usuarias y los Usuarios y de las Consumidoras y los Consumidores.</b> Garantiza información veraz sobre precios y condiciones, trato digno y el derecho a reclamar.</li>
      <li><b>Constitución Política del Estado, art. 21 y 130.</b> Reconocen el derecho a la privacidad e intimidad y la acción de protección de privacidad sobre tus datos.</li>
      <li><b>Ley N.º 164, General de Telecomunicaciones y TIC, y D.S. N.º 1793.</b> Dan validez a los documentos y firmas digitales, al comercio electrónico y a la protección de datos en servicios en línea.</li>
      <li><b>Código Tributario (Ley N.º 2492) y normativa del SIN.</b> Emitimos factura por cada venta según el sistema de facturación vigente.</li>
      <li><b>Ley N.º 045, contra el Racismo y toda forma de Discriminación.</b> Atendemos a todas las personas sin distinción de origen, nacionalidad, género o condición.</li>
      <li><b>Normativa de áreas protegidas y del Gobierno Autónomo Municipal de La Paz.</b> Respetamos las reglas de visita del SERNAP y contamos con licencia de funcionamiento municipal.</li>
    </ul>
    <h5>Reclamos</h5>
    <p>Si algo no salió como esperabas, escríbenos o visítanos en Calle Sagárnaga #234. Respondemos en un máximo de 5 días hábiles. Si no quedas conforme, puedes acudir al Viceministerio de Defensa de los Derechos del Usuario y del Consumidor.</p>
  </div>
  <div class="lm-foot"><button class="lm-ok" data-close>Entendido</button></div>
</dialog>

<div class="cookie" id="cookie">
  <span class="ci"><i class="fa-solid fa-cookie-bite"></i></span>
  <div class="ct"><b>Tu privacidad importa</b>Usamos cookies esenciales para que el sitio funcione. Las de analítica solo con tu permiso. <a href="#m-cookies" data-modal="m-cookies">Política de cookies</a></div>
  <div class="ca"><button data-c>Rechazar</button><button data-c>Configurar</button><button class="acc" data-c>Aceptar</button></div>
</div>
<button class="totop" id="totop" aria-label="Volver arriba"><i class="fa-solid fa-arrow-up"></i></button>

<script>
// header sólido al hacer scroll + botón arriba
const hdr=document.getElementById('hdr'),top_=document.getElementById('totop');
const onScroll=()=>{const y=scrollY;hdr.classList.toggle('solid',y>40);top_.classList.toggle('show',y>700)};
addEventListener('scroll',onScroll,{passive:true});onScroll();
top_.onclick=()=>scrollTo({top:0,behavior:'smooth'});

// menú móvil
const menu=document.getElementById('menu');
document.getElementById('openMenu').onclick=()=>{menu.classList.add('open');document.body.style.overflow='hidden'};
const closeM=()=>{menu.classList.remove('open');document.body.style.overflow=''};
document.getElementById('closeMenu').onclick=closeM;
menu.querySelectorAll('nav a,.mm-btns a').forEach(a=>a.addEventListener('click',closeM));

// reveal + contadores
const countUp=el=>{const t=+el.dataset.count,s=el.dataset.suf||'',d=1600,st=performance.now();
  const f=n=>{const p=Math.min((n-st)/d,1),v=Math.round(t*(1-Math.pow(1-p,3)));el.textContent=v.toLocaleString('es-BO')+s;if(p<1)requestAnimationFrame(f)};requestAnimationFrame(f)};
const io=new IntersectionObserver(es=>es.forEach(e=>{if(!e.isIntersecting)return;e.target.classList.add('in');
  e.target.querySelectorAll('[data-count]').forEach(countUp);io.unobserve(e.target)}),{threshold:.15});
document.querySelectorAll('.reveal,#mapbox').forEach(el=>io.observe(el));

// filtros del catálogo
const chips=document.querySelectorAll('.chip'),cards=document.querySelectorAll('.card');
const filter=f=>{chips.forEach(c=>c.classList.toggle('on',c.dataset.f===f));
  cards.forEach(c=>{const show=f==='todos'||c.dataset.cat===f;c.classList.toggle('hide',!show);if(show){c.classList.remove('in');void c.offsetWidth;c.classList.add('in')}})};
chips.forEach(c=>c.onclick=()=>filter(c.dataset.f));
document.querySelectorAll('[data-go]').forEach(t=>t.onclick=()=>{filter(t.dataset.go);document.getElementById('catalogo').scrollIntoView({behavior:'smooth'})});

// favoritos
document.querySelectorAll('.fav').forEach(b=>b.onclick=()=>{b.classList.toggle('on');b.querySelector('i').className=b.classList.contains('on')?'fa-solid fa-heart':'fa-regular fa-heart'});

// cookies
const ck=document.getElementById('cookie');
setTimeout(()=>ck.classList.add('show'),1400);
ck.querySelectorAll('[data-c]').forEach(b=>b.onclick=()=>ck.classList.remove('show'));
// modales legales
const openModal=id=>{const d=document.getElementById(id);if(!d||d.open)return;d.showModal();document.body.style.overflow='hidden';d.querySelector('.lm-body').scrollTop=0};
document.querySelectorAll('[data-modal]').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();openModal(a.dataset.modal)}));
document.querySelectorAll('.lmodal').forEach(d=>{
  d.querySelectorAll('[data-close]').forEach(b=>b.onclick=()=>d.close());
  d.addEventListener('click',e=>{if(e.target===d)d.close()});
  d.addEventListener('close',()=>{document.body.style.overflow=''});
});
</script>
</body>
</html>
