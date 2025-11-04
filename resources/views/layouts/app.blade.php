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
    <body class="font-sans antialiased relative">

        <div class="min-h-screen bg-gray-100 relative z-10">
            <!-- Header with Logo -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center">
                            <a href="/" class="flex items-center">
                                <img src="{{ asset('images/EC.jpeg') }}" alt="Eventandcode Logo" class="w-4 h-4 mr-3 rounded-full shadow-lg">
                                <span class="text-xl font-bold" style="color: #8B5CF6;">Eventandcode</span>
                            </a>
                        </div>
                        @include('layouts.navigation')
                    </div>
                </div>
            </header>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

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
