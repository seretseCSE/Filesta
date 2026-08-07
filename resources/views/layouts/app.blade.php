<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Finote Tsidik">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <title>@yield('title', 'Finote Tsidik')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-dvh bg-gray-50 font-sans text-gray-900 antialiased">
    <div id="offline-banner" class="fixed inset-x-0 top-0 z-[100] hidden bg-red-600 px-4 py-2 text-center text-sm font-medium text-white shadow-lg">
        No connection &mdash; reconnect to continue
    </div>

    <div id="ios-pwa-banner" class="fixed inset-x-0 top-0 z-[90] hidden bg-indigo-600 px-4 py-3 text-white shadow-lg pb-[env(safe-area-inset-top)]">
        <div class="flex items-start justify-between gap-3 pt-safe">
            <p class="text-sm font-medium leading-tight">
                Install this app on your iPhone: tap <span class="font-bold">Share</span>, then <span class="font-bold">Add to Home Screen</span>.
            </p>
            <button type="button" id="close-ios-banner" class="shrink-0 rounded-full p-1 text-indigo-200 hover:text-white active:bg-indigo-700">&times;</button>
        </div>
    </div>
    <main class="pb-28">
        @yield('content')
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <div class="mx-auto flex max-w-md items-center gap-3 px-4 py-3">
            @hasSection('actions')
                @yield('actions')
            @else
                <button type="button" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700">
                    Continue
                </button>
            @endif
        </div>
    </nav>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        // iOS Install Banner Logic
        const isIos = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            return /iphone|ipad|ipod/.test(userAgent);
        };
        const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

        if (isIos() && !isInStandaloneMode() && !localStorage.getItem('ios_banner_dismissed')) {
            const banner = document.getElementById('ios-pwa-banner');
            banner.classList.remove('hidden');
            document.getElementById('close-ios-banner').addEventListener('click', () => {
                banner.classList.add('hidden');
                localStorage.setItem('ios_banner_dismissed', 'true');
            });
        }

        // Offline / Online Status Logic
        const updateOnlineStatus = () => {
            const offlineBanner = document.getElementById('offline-banner');
            const disableElements = document.querySelectorAll('[data-offline-disable]');

            if (navigator.onLine) {
                offlineBanner.classList.add('hidden');
                disableElements.forEach(el => {
                    if (el.tagName === 'A') {
                        el.style.pointerEvents = 'auto';
                    } else {
                        el.disabled = false;
                    }
                    el.innerText = el.dataset.originalText || el.innerText;
                    el.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            } else {
                offlineBanner.classList.remove('hidden');
                disableElements.forEach(el => {
                    if (!el.dataset.originalText) {
                        el.dataset.originalText = el.innerText;
                    }
                    if (el.tagName === 'A') {
                        el.style.pointerEvents = 'none';
                    } else {
                        el.disabled = true;
                    }
                    el.innerText = 'No connection — reconnect to continue';
                    el.classList.add('opacity-50', 'cursor-not-allowed');
                });
            }
        };

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>
    @stack('scripts')
</body>
</html>
