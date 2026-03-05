<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->

        <!-- icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/imgs/logos/icono.png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Slim Select (buscador en selects de equipos) -->
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <livewire:start-checklist.modal />

        @livewireScripts
        <script>
            const runSlimSelectRefresh = () => {
                if (typeof window.refreshSlimSelects === 'function') window.refreshSlimSelects();
            };

            // Carga inicial
            document.addEventListener('DOMContentLoaded', runSlimSelectRefresh);

            // Para wire:navigate (Livewire v3)
            document.addEventListener('livewire:navigated', runSlimSelectRefresh);

            // Reaplicar después de actualizaciones de Livewire (v3)
            document.addEventListener('livewire:init', () => {
                runSlimSelectRefresh();

                if (window.Livewire && Livewire.hook) {
                    Livewire.hook('commit', ({ succeed }) => {
                        succeed(() => {
                            setTimeout(runSlimSelectRefresh, 0);
                        });
                    });
                }
            });
        </script>
    </body>
</html>
