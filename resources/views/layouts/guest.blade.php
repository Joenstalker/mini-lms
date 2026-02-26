<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      :data-theme="darkMode ? 'dark' : 'light'"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Mini-LMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            :root {
                --glass-bg: rgba(255, 255, 255, 0.08);
                --glass-blur: 1rem;
                --primary-sky: #0ea5e9;
                --primary-sky-hover: #0284c7;
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
                margin: 0;
                padding: 0;
            }

            .glass-style {
                background: var(--glass-bg) !important;
                backdrop-filter: blur(var(--glass-blur)) !important;
                -webkit-backdrop-filter: blur(var(--glass-blur)) !important;
                border: 0.0625rem solid rgba(255, 255, 255, 0.2) !important;
                box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.3) !important;
            }
            
            /* Typography Scaling */
            h2 { 
                font-size: clamp(1.5rem, 5vw, 2.25rem) !important; 
                line-height: 1.2 !important;
            }

            .label-text { 
                color: var(--text-sky) !important;
                font-weight: 800 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.15em !important;
                font-size: 0.6875rem !important; /* 11px */
            }
            
            .input-bordered {
                background-color: rgba(255, 255, 255, 0.05) !important;
                border: none !important;
                border-bottom: 0.125rem solid rgba(255, 255, 255, 0.2) !important;
                border-radius: 0 !important;
                color: #ffffff !important;
                transition: all 0.3s ease !important;
                height: 3rem !important;
                font-size: 1rem !important;
            }

            .btn-primary {
                background-color: var(--primary-sky) !important;
                border: none !important;
                color: #ffffff !important;
                font-weight: 900 !important;
                border-radius: 1rem !important;
                height: 3.5rem !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                box-shadow: 0 0.625rem 0.9375rem -0.1875rem rgba(14, 165, 233, 0.3) !important;
                transition: all 0.3s !important;
            }

            /* Mobile-First Responsiveness */
            .responsive-card {
                width: 95%;
                max-width: 40rem; /* ~640px */
                padding: 1.25rem;
                margin: 0.25rem auto;
                border-radius: 1rem !important;
            }

            /* Intermediate Laptop (1366px) */
            @media (min-width: 85.375rem) { /* 1366px */
                .responsive-card {
                    max-width: 45rem; /* Slightly wider for laptop */
                    padding: 2.5rem;
                }
                .label-text {
                    font-size: 0.75rem !important; /* 12px */
                }
            }

            /* Full Desktop (1920px) */
            @media (min-width: 120rem) { /* 1920px */
                .responsive-card {
                    max-width: 50rem;
                    padding: 3.5rem;
                }
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(1.25rem); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        </style>

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
    <body class="antialiased min-h-screen relative overflow-x-hidden">

        {{-- Subtle Dark Overlay --}}
        <div class="absolute inset-0 bg-black/40 z-0"></div>

        <div class="relative z-10 min-h-screen flex flex-col items-center justify-center p-[2vw]">
            
            {{-- Logo Area --}}
            <div class="mb-2 flex flex-col items-center gap-2 animate-fade-in-up">
                <a href="/" class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-2xl shadow-primary/30 transform hover:scale-110 transition-transform duration-500">
                    <svg class="w-10 h-10 text-primary-content" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    </svg>
                </a>
                <span class="text-[clamp(1.5rem,4vw,2.25rem)] font-extrabold tracking-tight text-white drop-shadow-md">Mini-LMS</span>
            </div>

            {{-- Floating Glass Card --}}
            <div class="glass-style animate-fade-in-up responsive-card" style="animation-delay: 0.2s;">
                <div class="overflow-hidden">
                    {{ $slot }}
                </div>

                {{-- Footer note --}}
                <div class="mt-2 text-center text-white/50 text-[0.65rem] font-medium">
                    &copy; {{ date('Y') }} Mini-LMS &mdash; Library Management System
                </div>
            </div>
        </div>

        <script>
            document.documentElement.setAttribute('data-theme', 'dark');
        </script>
    </body>
</html>
