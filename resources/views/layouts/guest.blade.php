<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :data-theme="darkMode ? 'dark' : 'light'" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mini-LMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-base-100 text-base-content selection:bg-primary selection:text-primary-content">
        <!-- Background Decoration -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] bg-secondary/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/" class="group flex flex-col items-center gap-4 no-underline">
                    <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center text-primary-content shadow-2xl shadow-primary/30 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold tracking-tight gradient-text">Mini-LMS</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-2">
                <div class="glass-card shadow-2xl border-white/20 overflow-hidden">
                    <div class="p-8">
                        {{ $slot }}
                    </div>
                </div>
                
                @if(Route::is('login'))
                    <p class="text-center mt-6 text-sm opacity-60">
                        Don't have an account? <a href="{{ route('register') }}" class="link link-primary font-bold">Sign up now</a>
                    </p>
                @elseif(Route::is('register'))
                    <p class="text-center mt-6 text-sm opacity-60">
                        Already have an account? <a href="{{ route('login') }}" class="link link-primary font-bold">Log in</a>
                    </p>
                @endif
            </div>
        </div>

        <script>
            // Theme initialization
            if (!localStorage.getItem('theme')) {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    localStorage.setItem('theme', 'dark');
                }
            }
        </script>
    </body>
</html>
