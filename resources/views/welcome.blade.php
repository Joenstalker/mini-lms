<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      :data-theme="darkMode ? 'dark' : 'light'"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Mini-LMS') }} - Admin Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --glass-bg: rgba(255, 255, 255, 0.08);
                --glass-blur: 1rem;
                --text-sky: #38bdf8;
            }

            body { 
                font-family: 'Outfit', sans-serif;
                background-image: url('{{ asset('images/login-page-background-photo.png') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                color: #ffffff;
            }

            .glass-style {
                background: var(--glass-bg) !important;
                backdrop-filter: blur(var(--glass-blur)) !important;
                -webkit-backdrop-filter: blur(var(--glass-blur)) !important;
                border: 0.0625rem solid rgba(255, 255, 255, 0.2) !important;
                box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.3) !important;
            }

            .btn-primary-glass {
                background-color: #0ea5e9 !important;
                border: none !important;
                color: #ffffff !important;
                font-weight: 900 !important;
                border-radius: 1.25rem !important;
                height: 3.5rem !important;
                box-shadow: 0 0.625rem 0.9375rem -0.1875rem rgba(14, 165, 233, 0.4) !important;
                transition: all 0.3s !important;
            }
            .btn-primary-glass:hover {
                background-color: #0284c7 !important;
                transform: translateY(-0.125rem);
                box-shadow: 0 1.25rem 1.5625rem -0.3125rem rgba(14, 165, 233, 0.5) !important;
            }

            .glass-input {
                background-color: rgba(255, 255, 255, 0.1) !important;
                border: none !important;
                border-bottom: 0.125rem solid rgba(255, 255, 255, 0.2) !important;
                border-radius: 0 !important;
                color: #ffffff !important;
                transition: all 0.3s ease !important;
            }
            .glass-input:focus {
                border-bottom-color: var(--text-sky) !important;
                background-color: rgba(255, 255, 255, 0.15) !important;
                outline: none !important;
            }

            /* Mobile-First Layout Adjustments */
            .main-container {
                padding: 1.5rem;
                flex-direction: column;
            }

            /* Tablet/Laptop Entry (1024px+) */
            @media (min-width: 64rem) {
                .main-container {
                    padding: 3vw 5vw;
                    flex-direction: row;
                    gap: 3rem;
                }
                h1 { font-size: 3rem !important; }
            }

            /* Intermediate Laptop (1366px) */
            @media (min-width: 85.375rem) {
                .main-container {
                    padding: 4vw 7vw;
                }
                h1 { font-size: 3.5rem !important; }
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(1.25rem); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
            @keyframes fadeInRight {
                from { opacity: 0; transform: translateX(1.25rem); }
                to { opacity: 1; transform: translateX(0); }
            }
            .animate-fade-in-right { animation: fadeInRight 1s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.showLoading = function(message = 'Processing...') {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    customClass: {
                        popup: 'rounded-3xl border-none shadow-3xl bg-slate-900 text-white p-12',
                        title: 'text-2xl font-bold text-white'
                    }
                });
            };
        </script>
    </head>
    <body class="antialiased min-h-screen relative overflow-x-hidden text-white">

        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/40 z-0"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-between main-container">
            
            {{-- Left Side: Heading Content --}}
            <div class="text-white w-full lg:max-w-2xl mb-12 lg:mb-0">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center shadow-xl shadow-primary/30">
                        <svg class="w-7 h-7 text-primary-content" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold tracking-tight">Mini-LMS</span>
                </div>
                
                <h1 class="text-[clamp(2.5rem,8vw,5rem)] font-extrabold leading-tight mb-6 animate-fade-in-up">
                    Welcome to the<br>Library System
                </h1>
                <p class="text-[clamp(1.1rem,3vw,1.5rem)] text-white/80 font-medium opacity-0 animate-fade-in-up" style="animation-delay: 0.3s;">
                    Log in to continue...
                </p>
                
                <div class="mt-6 flex flex-wrap gap-4 items-center opacity-0 animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="glass-style px-5 py-3 rounded-2xl">
                        <div class="text-2xl font-bold">Smart</div>
                        <div class="text-white/80 text-[0.6rem] uppercase tracking-widest font-bold">Automation</div>
                    </div>
                    <div class="glass-style px-5 py-3 rounded-2xl">
                        <div class="text-2xl font-bold">₱10</div>
                        <div class="text-white/80 text-[0.6rem] uppercase tracking-widest font-bold">Fine / Day</div>
                    </div>
                </div>
            </div>

            {{-- Right Side: High-Contrast Glass Login Card --}}
            <div class="w-full max-w-[min(95%,26rem)] sm:max-w-md responsive-card">
                <div class="glass-style rounded-[2rem] p-6 sm:p-8 lg:p-10 overflow-hidden animate-fade-in-right">
                    
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-extrabold text-white">Log in</h2>
                        <div class="w-10 h-1 bg-primary mx-auto mt-2 rounded-full"></div>
                    </div>

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="alert alert-success mb-6 bg-success/20 border-success/30 text-success text-sm py-3 px-4 rounded-xl">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-error mb-4 bg-error/20 border-error/30 text-error text-sm py-3 px-4 rounded-xl">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="showLoading('Signing you in...')">
                        @csrf

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label pb-2">
                                <span class="label-text text-sky-400 font-bold uppercase tracking-[0.2em] text-[0.7rem]">Email Address</span>
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   required autofocus autocomplete="username"
                                   placeholder="admin@example.com"
                                   class="input glass-input w-full" />
                        </div>

                        {{-- Password --}}
                        <div class="form-control">
                            <label class="label pb-2">
                                <span class="label-text text-sky-400 font-bold uppercase tracking-[0.2em] text-[0.7rem]">Password</span>
                            </label>
                            <input id="password" type="password" name="password"
                                   required autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="input glass-input w-full" />
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between mt-2">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm rounded-md bg-white/10 border-white/20">
                                <span class="ml-2 text-xs text-white/60 group-hover:text-white transition-colors">Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-sky-400 hover:text-sky-300 font-medium transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn btn-primary-glass w-full text-sm">
                                Sign In Now
                            </button>
                        </div>

                        <div class="text-center mt-8">
                            <p class="text-xs text-white/40 font-medium tracking-wide">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-sky-400 hover:text-sky-300 font-extrabold ml-1 transition-all underline decoration-sky-400/30 underline-offset-4">Sign up now</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.documentElement.setAttribute('data-theme', 'dark');
        </script>
    </body>
</html>
