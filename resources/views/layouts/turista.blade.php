<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Explorar tours') | Pipocas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --principal: #1F5F8B;
            --secundario: #C96F4A;
            --acento: #F4C95D;
            --complementario: #457699;
            --fondo: #F0F0F1;
            --texto: #343A40;
            --texto-2: #6B6F72;
            --calido: #6B4F3A;
            --hover: #174870;
        }
        * {
            box-sizing: border-box;
        }
        body {
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        button, a, input, select, textarea {
            -webkit-tap-highlight-color: transparent;
        }
        h1, h2, h3, .font-display {
            font-family: 'Fraunces', serif;
        }
        .tourist-shell {
            min-height: 100vh;
            background:
                radial-gradient(
                    ellipse at 85% 0%,
                    rgba(69,118,153,.08),
                    transparent 36%
                ),
                var(--fondo);
        }
        .tourist-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(16px);
            border-bottom: 3px solid var(--acento);
            box-shadow: 0 5px 20px rgba(31,58,80,.05);
        }
        .tourist-header-inner {
            max-width: 1800px;
            min-height: 82px;
            margin: auto;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .tourist-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .tourist-brand-icon {
            display: grid;
            place-items: center;
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            border-radius: 14px;
            background: var(--acento);
            color: var(--principal);
            box-shadow: 0 5px 15px rgba(0,0,0,.12);
        }
        .tourist-brand-name {
            color: var(--principal);
            font-size: 21px;
            font-weight: 800;
            line-height: 1.1;
        }
        .tourist-brand-name span {
            color: var(--secundario);
        }
        .tourist-brand-subtitle {
            color: var(--texto-2);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .tourist-header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }
        .tourist-welcome {
            margin-right: 10px;
            text-align: right;
        }
        .tourist-welcome-label {
            color: var(--complementario);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;
        }
        .tourist-welcome-name {
            color: var(--texto);
            font-size: 12px;
            font-weight: 800;
        }
        .tourist-nav-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 15px;
            border: 1px solid rgba(31,95,139,.15);
            border-radius: 12px;
            background: var(--principal);
            color: white;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            transition: .2s ease;
        }
        .tourist-nav-button:hover {
            background: var(--hover);
            transform: translateY(-1px);
        }
        .tourist-profile-button {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(201,111,74,.2);
            border-radius: 12px;
            background: rgba(244,201,93,.28);
            color: var(--principal);
            font-size: 16px;
            text-decoration: none;
            transition: .2s ease;
        }
        .tourist-profile-button:hover {
            background: var(--acento);
            color: var(--hover);
            transform: translateY(-1px);
        }
        .tourist-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 14px;
            border: 1px solid rgba(201,111,74,.25);
            border-radius: 12px;
            background: rgba(201,111,74,.10);
            color: var(--secundario);
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s ease;
        }
        .tourist-logout:hover {
            background: var(--secundario);
            color: white;
        }
        .tourist-content {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
            padding: 32px 28px;
        }
        .tourist-card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(31,95,139,.10);
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(31,58,80,.035);
            transition: box-shadow .25s ease;
        }
        .tourist-card:hover {
            box-shadow: 0 12px 30px rgba(31,58,80,.075);
        }
        .page-title {
            color: var(--hover);
            font-family: 'Fraunces', serif;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -.025em;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 10px;
            background: var(--principal);
            color: white;
            font-weight: 700;
            transition: .2s ease;
        }
        .btn-primary:hover {
            background: var(--hover);
        }
        .field {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #DDE3E8;
            border-radius: 11px;
            background: white;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .field:focus {
            border-color: var(--complementario);
            box-shadow: 0 0 0 3px rgba(69,118,153,.13);
        }
        .tourist-footer {
            padding: 22px 20px;
            color: var(--texto-2);
            text-align: center;
            font-size: 11px;
        }
        @media (max-width: 767px) {
            .tourist-header-inner {
                min-height: 70px;
                padding: 10px 14px;
                gap: 10px;
            }
            .tourist-brand-icon {
                width: 38px;
                height: 38px;
            }
            .tourist-brand-name {
                font-size: 18px;
            }
            .tourist-brand-subtitle {
                font-size: 8px;
            }
            .tourist-welcome {
                display: none;
            }
            .tourist-header-actions {
                gap: 6px;
            }
            .tourist-nav-button {
                min-height: 39px;
                padding: 9px 11px;
                font-size: 10px;
            }
            .tourist-profile-button {
                width: 39px;
                height: 39px;
            }
            .tourist-logout {
                width: 39px;
                height: 39px;
                min-height: 39px;
                padding: 0;
                font-size: 14px;
            }
            .tourist-logout .logout-label {
                display: none;
            }
            .tourist-content {
                padding: 22px 14px;
            }
            .page-title {
                font-size: 1.6rem;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased">
    <div class="tourist-shell">
        <header class="tourist-header">
            <div class="tourist-header-inner">
                <a href="{{ route('turista.tours.index') }}"
                   class="tourist-brand">
                    <div class="tourist-brand-icon">
                        <i class="fa-solid fa-mountain-sun text-xl"></i>
                    </div>
                    <div>
                        <div class="tourist-brand-name">
                            Pipocas<span>.</span>
                        </div>
                        <p class="tourist-brand-subtitle">
                            Agencia de turismo
                        </p>
                    </div>
                </a>
                <div class="tourist-header-actions">
                    <div class="tourist-welcome">
                        <p class="tourist-welcome-label">
                            Pipocas · Mi espacio
                        </p>
                        <p class="tourist-welcome-name">
                            Bienvenido, {{ auth()->user()->nom }}
                        </p>
                    </div>
                    <a href="{{ route('turista.reservas.index') }}"
                       class="tourist-nav-button">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Mis reservas</span>
                    </a>
                    <a href="{{ route('turista.perfil') }}"
                       class="tourist-profile-button"
                       title="Mi perfil"
                       aria-label="Ir a mi perfil">
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <form id="logout-form"
                          method="POST"
                          action="{{ route('logout') }}">
                        @csrf
                        <button type="button"
                                id="logout-button"
                                class="tourist-logout"
                                title="Cerrar sesión"
                                aria-label="Cerrar sesión">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span class="logout-label">Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>
        <main class="tourist-content">
            @yield('content')
        </main>
        <footer class="tourist-footer">
            © {{ date('Y') }} Pipocas · Turismo.
            Todos los derechos reservados.
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));
            const validationErrors = @json($errors->all());
            if (typeof window.Swal !== 'undefined') {
                if (validationErrors.length > 0) {
                    window.Swal.fire({
                        title: 'Revisa los datos',
                        text: validationErrors.join(' | '),
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#1F5F8B'
                    });
                } else if (errorMessage) {
                    window.Swal.fire({
                        title: 'Ocurrió un problema',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#1F5F8B'
                    });
                } else if (successMessage) {
                    window.Swal.fire({
                        title: '¡Operación exitosa!',
                        text: successMessage,
                        icon: 'success',
                        confirmButtonText: 'Continuar',
                        confirmButtonColor: '#1F5F8B'
                    });
                }
            } else {
                if (validationErrors.length > 0) {
                    alert(validationErrors.join('\n'));
                } else if (errorMessage) {
                    alert(errorMessage);
                } else if (successMessage) {
                    alert(successMessage);
                }
            }
            const logoutButton = document.getElementById('logout-button');
            const logoutForm = document.getElementById('logout-form');
            if (!logoutButton || !logoutForm) {
                return;
            }
            logoutButton.addEventListener('click', function () {
                if (typeof window.Swal === 'undefined') {
                    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                        logoutForm.submit();
                    }
                    return;
                }
                window.Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: '¿Estás seguro de que deseas salir de tu cuenta?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#1F5F8B',
                    cancelButtonColor: '#C96F4A',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>