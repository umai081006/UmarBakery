<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Umar Bakery') }} - Sign In</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans text-cocoa antialiased selection:bg-butter selection:text-cocoa">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cream relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(circle_800px_at_50%_200px,_var(--tw-gradient-stops))] from-caramel via-transparent to-transparent"></div>
            
            <div class="mb-8 relative z-10">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 group">
                    <span class="font-serif text-3xl font-bold text-cocoa tracking-tight">
                        Umar <span class="text-caramel italic">Bakery</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-float overflow-hidden rounded-4xl relative z-10">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-cocoa/30 font-mono relative z-10">&copy; {{ date('Y') }} Umar Bakery. All rights reserved.</p>
        </div>
    </body>
</html>
