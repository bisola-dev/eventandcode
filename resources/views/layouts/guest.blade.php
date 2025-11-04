<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Eventandcode') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col bg-gray-100">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-center items-center py-4">
                        <a href="/" class="flex items-center">
                            <img src="{{ asset('images/EC.jpeg') }}" alt="Eventandcode Logo" class="w-6 h-6 mr-3 rounded-full shadow-lg">
                            <span class="text-2xl font-bold" style="color: #8B5CF6;">Eventandcode</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t mt-auto">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-sm" style="color: #8B5CF6;">
                        <p>&copy; {{ date('Y') }} Eventandcode. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
