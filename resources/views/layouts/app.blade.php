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
    </script>
    @stack('scripts')
</body>
</html>
