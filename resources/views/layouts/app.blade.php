<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :data-theme="darkMode ? 'dark' : 'light'" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mini-LMS') }} - Smart Library Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-base-100 text-base-content min-h-screen selection:bg-primary selection:text-primary-content">
        <!-- Background Decoration -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] bg-secondary/10 rounded-full blur-[100px]"></div>
        </div>

        @auth
        <div class="drawer lg:drawer-open">
            <input id="main-drawer" type="checkbox" class="drawer-toggle" />
            
            <div class="drawer-content flex flex-col min-h-screen">
                <!-- Top Navbar (Slim) -->
                <nav class="sticky top-0 z-40 flex h-16 w-full justify-center bg-base-100/60 backdrop-blur-md border-b border-base-200 px-4">
                    <div class="flex w-full items-center justify-between">
                        <!-- Mobile Hamburguer -->
                        <div class="flex lg:hidden">
                            <label for="main-drawer" class="btn btn-ghost btn-circle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </label>
                        </div>

                        <!-- Brand (Mobile Only) -->
                        <div class="flex lg:hidden flex-1 px-2">
                            <span class="text-xl font-bold gradient-text">Mini-LMS</span>
                        </div>

                        <!-- Top Right Actions -->
                        <div class="flex items-center gap-2 ml-auto">
                            <!-- Theme Toggle -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="btn btn-ghost btn-circle btn-sm">
                                <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </button>

                            <div class="divider divider-horizontal mx-1"></div>

                            <!-- User context info (Desktop only) -->
                            <div class="hidden md:flex flex-col items-end mr-2">
                                <span class="text-xs font-bold">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] opacity-50 uppercase tracking-tighter">Library Admin</span>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Main Content Area -->
                <main class="flex-grow container mx-auto px-4 py-8 max-w-7xl animate-in fade-in slide-in-from-bottom-4 duration-700">
                    @if (session('status'))
                        <div class="alert alert-info glass mb-6 border-l-4 border-info rounded-xl">
                            <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success glass mb-6 border-l-4 border-success rounded-xl">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error glass mb-6 border-l-4 border-error rounded-xl">
                            <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div class="flex flex-col gap-1">
                                @foreach ($errors->all() as $error)
                                    <span class="text-sm font-medium">{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="bg-base-200/50 backdrop-blur-sm border-t border-base-200 pt-16 pb-8">
                    <div class="container mx-auto px-4 text-center">
                        <p class="text-xs text-base-content/40">&copy; {{ date('Y') }} Mini-LMS. Smart Library Management.</p>
                    </div>
                </footer>
            </div>

            <!-- Sidebar -->
            <div class="drawer-side z-50">
                <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <x-sidebar />
            </div>
        </div>
        @else
        <!-- Guest View -->
        <nav class="sticky top-0 z-50 transition-all duration-300 glass border-b border-base-200">
            <div class="container mx-auto px-4">
                <div class="navbar h-20">
                    <div class="flex-1">
                        <a href="{{ route('home') }}" class="group flex items-center gap-3 no-underline">
                            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-primary-content shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold tracking-tight gradient-text">Mini-LMS</span>
                        </a>
                    </div>
                    <div class="flex-none gap-4">
                        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="btn btn-ghost btn-circle">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm rounded-lg">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-lg px-6 shadow-lg shadow-primary/20">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
            {{ $slot }}
        </main>
        <footer class="bg-base-200/50 backdrop-blur-sm border-t border-base-200 py-8">
            <div class="container mx-auto px-4 text-center">
                <p class="text-xs text-base-content/40">&copy; {{ date('Y') }} Mini-LMS. Smart Library Management.</p>
            </div>
        </footer>
        @endauth

        <!-- Custom Initialization -->
        <script>
            // Check for system preference
            if (!localStorage.getItem('theme')) {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    localStorage.setItem('theme', 'dark');
                }
            }
        </script>
    </body>
</html>
