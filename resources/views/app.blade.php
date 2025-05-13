<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Aggressively force light theme --}}
        <script>
            (function() {
                // Remove dark class immediately if exists
                document.documentElement.classList.remove('dark');
                // Clear any stored theme preferences
                if (typeof localStorage !== 'undefined') {
                    localStorage.removeItem('theme');
                }
                // Add light theme marker
                document.documentElement.setAttribute('data-theme', 'light');
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            /* Ensure body also has light background */
            body {
                background-color: white;
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('assets/Logo_polos.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
