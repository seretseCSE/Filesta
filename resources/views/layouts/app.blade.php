<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Filseta">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <title>@yield('title', 'Filseta')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- Offline banner --}}
    <div id="offline-banner" class="fixed inset-x-0 top-0 z-50 hidden bg-red-600 px-4 py-2 text-center text-sm font-medium text-white">
        No connection — reconnect to continue
    </div>

    {{-- iOS install banner --}}
    <div id="ios-pwa-banner" class="fixed inset-x-0 top-0 z-40 hidden bg-indigo-700 px-4 py-3 text-white">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm">Tap <strong>Share</strong> then <strong>Add to Home Screen</strong> to install.</p>
            <button type="button" id="close-ios-banner" class="shrink-0 text-indigo-200 text-xl leading-none">&times;</button>
        </div>
    </div>

    {{-- Page content --}}
    <main class="min-h-dvh pb-24">
        @yield('content')
    </main>

    {{-- Bottom action bar --}}
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white" style="padding-bottom: env(safe-area-inset-bottom)">
        <div class="flex gap-3 px-4 py-3">
            @hasSection('actions')
                @yield('actions')
            @else
                {{-- nothing --}}
            @endif
        </div>
    </nav>

    <script>
        // Service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }

        // iOS install banner
        var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
        var isStandalone = window.navigator.standalone === true;
        if (isIos && !isStandalone && !localStorage.getItem('ios_dismissed')) {
            var iosBanner = document.getElementById('ios-pwa-banner');
            iosBanner.classList.remove('hidden');
            document.getElementById('close-ios-banner').addEventListener('click', function () {
                iosBanner.classList.add('hidden');
                localStorage.setItem('ios_dismissed', '1');
            });
        }

        // Offline indicator
        function updateOnlineStatus() {
            var offlineBanner = document.getElementById('offline-banner');
            var targets = document.querySelectorAll('[data-offline-disable]');

            if (navigator.onLine) {
                offlineBanner.classList.add('hidden');
                targets.forEach(function (el) {
                    el.disabled = false;
                    el.style.pointerEvents = '';
                    el.style.opacity = '';
                    if (el.dataset.originalText) {
                        el.textContent = el.dataset.originalText;
                    }
                });
            } else {
                offlineBanner.classList.remove('hidden');
                targets.forEach(function (el) {
                    if (!el.dataset.originalText) {
                        el.dataset.originalText = el.textContent.trim();
                    }
                    el.disabled = true;
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.5';
                });
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>
    @stack('scripts')
</body>
</html>
