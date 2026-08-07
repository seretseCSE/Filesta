<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Filseta">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <title>@yield('title', 'Filseta') — Filseta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        * { box-sizing: border-box; }
        body { font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .app-bar { position: fixed; top: 0; left: 0; right: 0; z-index: 50; background: #1e293b; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; height: 56px; gap: 12px; }
        .app-bar h1 { font-size: 1rem; font-weight: 700; margin: 0; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .app-bar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .app-bar a, .app-bar button[type=submit] { color: #94a3b8; font-size: 0.875rem; background: none; border: none; cursor: pointer; padding: 8px 4px; text-decoration: none; white-space: nowrap; }
        .app-bar a:hover, .app-bar button[type=submit]:hover { color: #fff; }
        .app-bar a.btn-primary { background: #4f46e5; color: #fff; padding: 8px 14px; border-radius: 6px; font-weight: 600; }
        .app-bar a.btn-primary:hover { background: #4338ca; }
        .signout-btn { background: #dc2626; color: #fff; border: none; border-radius: 6px; padding: 8px 14px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; white-space: nowrap; }
        .signout-btn:active { background: #b91c1c; }
        .signout-btn:disabled { opacity: 0.5; pointer-events: none; }
        .offline-bar { display: none; position: fixed; top: 56px; left: 0; right: 0; z-index: 49; background: #dc2626; color: #fff; text-align: center; font-size: 0.8125rem; font-weight: 600; padding: 8px 16px; }
        main { padding-top: 72px; padding-bottom: 80px; }
        .page { max-width: 640px; margin: 0 auto; padding: 0 16px 24px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; }
        .section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 24px 0 10px; }
        /* Inputs */
        .field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 14px; }
        .field label, .field-label { font-size: 0.8125rem; font-weight: 600; color: #374151; }
        .field input, .field select, input.form-input, select.form-select {
            width: 100%; border: 1px solid #cbd5e1; border-radius: 6px;
            padding: 12px 14px; font-size: 1rem; color: #0f172a; background: #fff;
            appearance: auto; -webkit-appearance: auto;
        }
        .field input:focus, .field select:focus, input.form-input:focus, select.form-select:focus { outline: 2px solid #4f46e5; outline-offset: -1px; }
        /* Alerts */
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; border-radius: 6px; padding: 12px 14px; font-size: 0.875rem; margin-bottom: 16px; }
        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; border-radius: 6px; padding: 12px 14px; font-size: 0.875rem; margin-bottom: 16px; }
        /* Lists */
        .list-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
        .list-row:last-child { border-bottom: none; }
        .list-row-title { font-weight: 600; font-size: 0.9375rem; }
        .list-row-sub { font-size: 0.8125rem; color: #64748b; margin-top: 2px; }
        .list-row-amount { font-weight: 700; font-size: 0.9375rem; margin-top: 4px; }
        .list-actions { display: flex; gap: 8px; flex-shrink: 0; align-items: center; }
        /* Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 0.875rem; font-weight: 600; padding: 9px 16px; cursor: pointer; text-decoration: none; border: none; white-space: nowrap; min-height: 44px; }
        .btn-sm { padding: 7px 12px; font-size: 0.8125rem; min-height: 38px; }
        .btn-indigo { background: #4f46e5; color: #fff; }
        .btn-indigo:active { background: #4338ca; }
        .btn-red { background: #dc2626; color: #fff; }
        .btn-red:active { background: #b91c1c; }
        .btn-outline { background: #fff; color: #374151; border: 1px solid #cbd5e1; }
        .btn-outline:active { background: #f8fafc; }
        .btn-ghost-red { background: none; border: 1px solid #fca5a5; color: #dc2626; }
        .btn-ghost-red:active { background: #fef2f2; }
        /* Bottom action bar */
        .bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 50; background: #fff; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; padding: 12px 16px; padding-bottom: calc(12px + env(safe-area-inset-bottom)); }
        .bottom-bar .btn { flex: 1; min-height: 52px; font-size: 1rem; }
        .bottom-bar .btn-outline { flex: 0 0 auto; width: auto; padding: 0 20px; }
        /* Summary cards */
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 8px; }
        .stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
        .stat-label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .stat-value { font-size: 1.375rem; font-weight: 800; margin-top: 4px; color: #0f172a; }
        .stat-value.green { color: #15803d; }
        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .data-table th { text-align: left; padding: 10px 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .data-table th:not(:first-child) { text-align: right; }
        .data-table td { padding: 11px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .data-table td:not(:first-child) { text-align: right; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table .empty td { text-align: center; color: #94a3b8; padding: 24px; }
        /* Stepper */
        .stepper { display: flex; align-items: center; gap: 10px; }
        .stepper-btn { width: 52px; height: 52px; border-radius: 50%; border: 1px solid #cbd5e1; background: #fff; font-size: 1.5rem; line-height: 1; color: #374151; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stepper-btn:active { background: #f1f5f9; }
        .stepper input { flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; text-align: center; font-size: 1.25rem; font-weight: 700; }
        /* Status badge */
        .badge { display: inline-block; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 999px; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        /* Install banner */
        .install-banner { display: none; position: fixed; top: 56px; left: 0; right: 0; z-index: 48; background: #312e81; color: #e0e7ff; font-size: 0.8125rem; padding: 10px 16px; }
        .install-banner-inner { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .install-banner .install-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .install-banner .btn-install { background: #4f46e5; color: #fff; border: none; border-radius: 6px; padding: 7px 14px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; white-space: nowrap; }
        .install-banner .btn-install:active { background: #4338ca; }
        .install-banner .btn-install:disabled { opacity: 0.5; pointer-events: none; }
        .install-banner .dismiss { background: none; border: none; color: #a5b4fc; font-size: 1.25rem; cursor: pointer; padding: 0; line-height: 1; }
    </style>
</head>
<body>

    {{-- Top app bar --}}
    <header class="app-bar">
        <h1>@yield('page-title', 'Filseta')</h1>
        <div class="app-bar-actions">
            @yield('app-bar-actions')
            @auth
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" data-offline-disable class="signout-btn">Sign out</button>
                </form>
            @endauth
        </div>
    </header>

    {{-- Offline banner --}}
    <div id="offline-bar" class="offline-bar">
        No connection — reconnect to continue
    </div>

    {{-- Install banner --}}
    <div id="install-banner" class="install-banner">
        <div class="install-banner-inner">
            <span id="install-message">Install this app on your device for quick access.</span>
            <div class="install-actions">
                <button type="button" id="install-app-btn" class="btn-install" style="display:none;">Install app</button>
                <button type="button" id="close-install" class="dismiss" aria-label="Dismiss">&times;</button>
            </div>
        </div>
    </div>

    <main>
        <div class="page">
            @yield('content')
        </div>
    </main>

    @hasSection('bottom-bar')
        <div class="bottom-bar">
            @yield('bottom-bar')
        </div>
    @else
        @hasSection('actions')
            <div class="bottom-bar">
                @yield('actions')
            </div>
        @endif
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }

        // Install app banner
        (function () {
            var banner = document.getElementById('install-banner');
            var message = document.getElementById('install-message');
            var installBtn = document.getElementById('install-app-btn');
            var closeBtn = document.getElementById('close-install');
            var deferredPrompt = null;
            var isStandalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;
            var isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
            var installed = localStorage.getItem('install_installed') === '1';
            var dismissedThisSession = sessionStorage.getItem('install_dismissed_session') === '1';

            if (!banner || isStandalone || installed || dismissedThisSession) return;

            function show() { banner.style.display = 'block'; }
            function hide() { banner.style.display = 'none'; }

            // Once the app is installed, never ask again
            if (isStandalone) {
                localStorage.setItem('install_installed', '1');
                return;
            }

            // iOS Safari: no install prompt event, guide to Share -> Add to Home Screen
            if (isIOS) {
                message.textContent = 'Tap Share, then Add to Home Screen to install this app.';
                show();
            }

            // Android / desktop Chrome & Edge
            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                deferredPrompt = e;
                if (!isIOS) {
                    installBtn.style.display = 'inline-block';
                    message.textContent = 'Install this app on your device for quick access.';
                    show();
                }
            });

            installBtn.addEventListener('click', function () {
                if (!deferredPrompt) return;
                installBtn.disabled = true;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (choice) {
                    deferredPrompt = null;
                    if (choice.outcome === 'accepted') {
                        localStorage.setItem('install_installed', '1');
                    }
                    hide();
                });
            });

            // Dismiss hides it for this tab session only; it returns on the next page load
            closeBtn.addEventListener('click', function () {
                hide();
                sessionStorage.setItem('install_dismissed_session', '1');
            });
        })();

        // Offline
        function syncOnlineStatus() {
            var bar = document.getElementById('offline-bar');
            var targets = document.querySelectorAll('[data-offline-disable]');
            if (navigator.onLine) {
                bar.style.display = 'none';
                targets.forEach(function (el) {
                    el.disabled = false;
                    el.style.opacity = '';
                    el.style.pointerEvents = '';
                    if (el.dataset.orig) { el.textContent = el.dataset.orig; }
                });
            } else {
                bar.style.display = 'block';
                targets.forEach(function (el) {
                    if (!el.dataset.orig) el.dataset.orig = el.textContent.trim();
                    el.disabled = true;
                    el.style.opacity = '0.5';
                    el.style.pointerEvents = 'none';
                });
            }
        }
        window.addEventListener('online', syncOnlineStatus);
        window.addEventListener('offline', syncOnlineStatus);
        syncOnlineStatus();
    </script>
    @stack('scripts')
</body>
</html>
