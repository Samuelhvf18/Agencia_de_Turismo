<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .pipocas-legal {
            --legal-blue: #1F5F8B;
            --legal-terracotta: #C96F4A;
            --legal-gold: #F4C95D;
            --legal-soft-blue: #DCEAF3;
            --legal-bg: #F0F0F1;
            --legal-text: #343A40;
            --legal-muted: #6B6F72;
            position: relative;
            isolation: isolate;
            min-height: 100vh;
            padding: 34px 20px 26px;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 0% 0%, rgba(244, 201, 93, .19), transparent 32%),
                radial-gradient(ellipse at 100% 25%, rgba(31, 95, 139, .10), transparent 32%),
                var(--legal-bg);
            color: var(--legal-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .pipocas-legal *,
        .pipocas-legal *::before,
        .pipocas-legal *::after {
            box-sizing: border-box;
        }
        .pipocas-legal__container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }
        .pipocas-legal__decoration {
            position: absolute;
            z-index: -1;
            pointer-events: none;
            border-radius: 50%;
        }
        .pipocas-legal__decoration--one {
            top: 230px;
            left: -105px;
            width: 220px;
            height: 220px;
            border: 35px solid rgba(201, 111, 74, .08);
        }
        .pipocas-legal__decoration--two {
            top: 560px;
            right: -100px;
            width: 240px;
            height: 240px;
            border: 38px solid rgba(31, 95, 139, .06);
        }
        .pipocas-legal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 38px;
        }
        .pipocas-legal__brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--legal-blue);
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1px;
            text-decoration: none;
        }
        .pipocas-legal__brand-icon {
            display: grid;
            width: 49px;
            height: 49px;
            flex-shrink: 0;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(145deg, var(--legal-blue), #347BAA);
            box-shadow: 0 7px 17px rgba(31, 95, 139, .22);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .pipocas-legal__brand:hover .pipocas-legal__brand-icon {
            transform: rotate(-5deg) translateY(-2px);
            box-shadow: 0 10px 22px rgba(31, 95, 139, .28);
        }
        .pipocas-legal__brand-icon svg {
            width: 30px;
            height: 30px;
        }
        .pipocas-legal__brand-name {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .pipocas-legal__brand-name small {
            color: var(--legal-muted);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .pipocas-legal__back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 12px 16px;
            border: 1px solid rgba(31, 95, 139, .13);
            border-radius: 13px;
            background: rgba(255, 255, 255, .85);
            color: var(--legal-blue);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(31, 95, 139, .04);
            transition: background .2s ease, color .2s ease,
                        transform .2s ease, box-shadow .2s ease;
        }
        .pipocas-legal__back:hover {
            transform: translateY(-2px);
            background: var(--legal-blue);
            color: #FFFFFF;
            box-shadow: 0 7px 18px rgba(31, 95, 139, .17);
        }
        .pipocas-legal__back svg {
            width: 16px;
            height: 16px;
        }
        .pipocas-legal__intro {
            position: relative;
            margin-bottom: 30px;
            text-align: center;
        }
        .pipocas-legal__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 17px;
            padding: 9px 15px;
            border: 1px solid rgba(201, 111, 74, .18);
            border-radius: 999px;
            background: #FFF4EE;
            color: #A94F2E;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .pipocas-legal__eyebrow svg {
            width: 15px;
            height: 15px;
            color: var(--legal-terracotta);
        }
        .pipocas-legal__title {
            margin: 0;
            color: var(--legal-blue);
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(32px, 5vw, 46px);
            font-weight: 700;
            letter-spacing: -1.2px;
            line-height: 1.2;
        }
        .pipocas-legal__title::after {
            display: block;
            width: 65px;
            height: 4px;
            margin: 17px auto 0;
            border-radius: 99px;
            background: linear-gradient(
                90deg,
                var(--legal-terracotta) 0 48%,
                var(--legal-gold) 48% 100%
            );
            content: '';
        }
        .pipocas-legal__subtitle {
            max-width: 590px;
            margin: 16px auto 0;
            color: var(--legal-muted);
            font-size: 13px;
            line-height: 1.9;
        }
        .pipocas-legal__card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(31, 95, 139, .10);
            border-radius: 24px;
            background: #FFFFFF;
            box-shadow:
                0 20px 55px rgba(31, 55, 75, .07),
                0 3px 9px rgba(31, 55, 75, .025);
        }
        .pipocas-legal__card-accent {
            height: 6px;
            background: linear-gradient(
                90deg,
                var(--legal-blue) 0%,
                #347BAA 45%,
                var(--legal-gold) 72%,
                var(--legal-terracotta) 100%
            );
        }
        .pipocas-legal__card-inner {
            padding: 39px 44px 43px;
        }
        .pipocas-legal__content {
            overflow-wrap: anywhere;
            color: var(--legal-text);
            font-size: 13px;
            line-height: 1.95;
        }
        .pipocas-legal__content > h1:first-child {
            display: none;
        }
        .pipocas-legal__content h2 {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin: 34px 0 15px;
            padding-bottom: 11px;
            border-bottom: 1px solid #E8EDF1;
            color: var(--legal-blue);
            font-family: 'Fraunces', Georgia, serif;
            font-size: 23px;
            font-weight: 700;
            line-height: 1.45;
        }
        .pipocas-legal__content h2::before {
            width: 5px;
            min-width: 5px;
            height: 22px;
            border-radius: 5px;
            background: linear-gradient(
                180deg,
                var(--legal-terracotta),
                var(--legal-gold)
            );
            content: '';
        }
        .pipocas-legal__content h3 {
            margin: 25px 0 10px;
            color: var(--legal-blue);
            font-size: 15px;
            font-weight: 800;
            line-height: 1.6;
        }
        .pipocas-legal__content p {
            margin: 0 0 17px;
        }
        .pipocas-legal__content ul,
        .pipocas-legal__content ol {
            margin: 13px 0 24px;
            padding-left: 25px;
        }
        .pipocas-legal__content li {
            margin-bottom: 10px;
            padding-left: 5px;
        }
        .pipocas-legal__content li::marker {
            color: var(--legal-terracotta);
            font-weight: 800;
        }
        .pipocas-legal__content strong {
            color: #293E4D;
            font-weight: 800;
        }
        .pipocas-legal__content a {
            color: var(--legal-blue);
            font-weight: 700;
            text-decoration: underline;
            text-decoration-color: rgba(31, 95, 139, .35);
            text-underline-offset: 3px;
            transition: color .2s ease;
        }
        .pipocas-legal__content a:hover {
            color: var(--legal-terracotta);
            text-decoration-color: var(--legal-terracotta);
        }
        .pipocas-legal__content blockquote {
            margin: 22px 0;
            padding: 17px 21px;
            border-left: 4px solid var(--legal-gold);
            border-radius: 0 13px 13px 0;
            background: #FFF9E8;
            color: #65552F;
        }
        .pipocas-legal__content blockquote p:last-child {
            margin-bottom: 0;
        }
        .pipocas-legal__content hr {
            margin: 28px 0;
            border: 0;
            border-top: 1px solid #E8EDF1;
        }
        .pipocas-legal__footer {
            padding-top: 27px;
            text-align: center;
        }
        .pipocas-legal__footer-note {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 17px;
            color: var(--legal-muted);
            font-size: 11px;
            line-height: 1.8;
        }
        .pipocas-legal__footer-note svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            color: var(--legal-terracotta);
        }
        .pipocas-legal__links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .pipocas-legal__links a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 13px;
            border: 1px solid rgba(31, 95, 139, .10);
            border-radius: 10px;
            background: rgba(255, 255, 255, .65);
            color: var(--legal-blue);
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            transition: background .2s ease, color .2s ease,
                        transform .2s ease;
        }
        .pipocas-legal__links a:hover {
            transform: translateY(-2px);
            background: var(--legal-blue);
            color: #FFFFFF;
        }
        .pipocas-legal__links svg {
            width: 15px;
            height: 15px;
        }
        .pipocas-legal__copyright {
            margin-top: 20px;
            color: #8A8E91;
            font-size: 10px;
        }
        .pipocas-legal a:focus-visible {
            outline: 3px solid var(--legal-gold);
            outline-offset: 4px;
        }
        @media (max-width: 600px) {
            .pipocas-legal {
                padding: 20px 13px 22px;
            }
            .pipocas-legal__header {
                gap: 10px;
                margin-bottom: 34px;
            }
            .pipocas-legal__brand {
                gap: 9px;
                font-size: 21px;
            }
            .pipocas-legal__brand-icon {
                width: 42px;
                height: 42px;
                border-radius: 13px;
            }
            .pipocas-legal__brand-icon svg {
                width: 26px;
                height: 26px;
            }
            .pipocas-legal__brand-name small {
                font-size: 8px;
                letter-spacing: 1.4px;
            }
            .pipocas-legal__back {
                gap: 5px;
                padding: 10px 11px;
                font-size: 10px;
            }
            .pipocas-legal__intro {
                margin-bottom: 24px;
            }
            .pipocas-legal__eyebrow {
                margin-bottom: 13px;
                padding: 8px 12px;
                font-size: 9px;
            }
            .pipocas-legal__title {
                font-size: clamp(29px, 8vw, 37px);
            }
            .pipocas-legal__subtitle {
                margin-top: 13px;
                font-size: 12px;
            }
            .pipocas-legal__card {
                border-radius: 18px;
            }
            .pipocas-legal__card-inner {
                padding: 25px 19px 29px;
            }
            .pipocas-legal__content {
                font-size: 12px;
                line-height: 1.9;
            }
            .pipocas-legal__content h2 {
                gap: 8px;
                margin-top: 27px;
                font-size: 20px;
            }
            .pipocas-legal__content h2::before {
                height: 19px;
            }
            .pipocas-legal__content h3 {
                font-size: 14px;
            }
            .pipocas-legal__footer-note {
                font-size: 10px;
            }
            .pipocas-legal__links {
                gap: 8px;
            }
            .pipocas-legal__links a {
                padding: 9px 10px;
                font-size: 10px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .pipocas-legal *,
            .pipocas-legal *::before,
            .pipocas-legal *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
    <div class="pipocas-legal">
        <div class="pipocas-legal__decoration pipocas-legal__decoration--one"></div>
        <div class="pipocas-legal__decoration pipocas-legal__decoration--two"></div>
        <div class="pipocas-legal__container">
            <header class="pipocas-legal__header">
                <a href="{{ url('/') }}" class="pipocas-legal__brand"
                   aria-label="Pipocas, ir al inicio">
                    <span class="pipocas-legal__brand-icon" aria-hidden="true">
                        <svg viewBox="0 0 40 40" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 35C20 35 7.5 23.8 7.5 15.8C7.5 8.9 13.1 4 20 4C26.9 4 32.5 8.9 32.5 15.8C32.5 23.8 20 35 20 35Z"
                                  fill="#F4C95D"/>
                            <circle cx="20" cy="15.5" r="8.5" fill="#FFFFFF"/>
                            <path d="M16.3 19.2L17.2 15.4L20 10.5L22.8 15.4L23.7 19.2L20 17.7L16.3 19.2Z"
                                  fill="#1F5F8B"/>
                            <circle cx="20" cy="15.5" r="2.2" fill="#C96F4A"/>
                            <path d="M12 31.5L9.5 34M28 31.5L30.5 34"
                                  stroke="#FFFFFF" stroke-width="2.2"
                                  stroke-linecap="round" opacity=".9"/>
                        </svg>
                    </span>
                    <span class="pipocas-legal__brand-name">
                        <span>Pipocas</span>
                        <small>Descubre y viaja</small>
                    </span>
                </a>
                <a href="{{ route('login') }}" class="pipocas-legal__back">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M19 12H5"/>
                        <path d="M12 19l-7-7 7-7"/>
                    </svg>
                    Volver al acceso
                </a>
            </header>
            <section class="pipocas-legal__intro">
                <span class="pipocas-legal__eyebrow">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                    Información legal
                </span>
                <h1 class="pipocas-legal__title">
                    @yield('legal-title')
                </h1>
                <p class="pipocas-legal__subtitle">
                    Queremos que viajes con tranquilidad.
                    Conoce las condiciones de uso y las prácticas de privacidad
                    que ayudan a proteger tu experiencia en Pipocas.
                </p>
            </section>
            <main class="pipocas-legal__card">
                <div class="pipocas-legal__card-accent"></div>
                <div class="pipocas-legal__card-inner">
                    <article class="pipocas-legal__content">
                        @yield('legal-content')
                    </article>
                </div>
            </main>
            <footer class="pipocas-legal__footer">
                <div class="pipocas-legal__footer-note">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z"/>
                    </svg>
                    Información clara para viajar con confianza.
                </div>
                <nav class="pipocas-legal__links"
                     aria-label="Documentos legales">
                    <a href="{{ route('terms.show') }}">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M8 13h8M8 17h8"/>
                        </svg>
                        Términos y condiciones
                    </a>
                    <a href="{{ route('policy.show') }}">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                        Política de privacidad
                    </a>
                </nav>
                <div class="pipocas-legal__copyright">
                    © {{ date('Y') }} Pipocas · Todos los derechos reservados.
                </div>
            </footer>
        </div>
    </div>
</x-guest-layout>