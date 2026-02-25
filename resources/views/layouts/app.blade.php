<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches), 
          showProfileModal: false,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
      }" 
      :data-theme="darkMode ? 'dark' : 'light'" 
      :class="{ 'dark': darkMode }"
      class="overflow-y-scroll">
    <head>
        <script>
            (function() {
                const theme = localStorage.getItem('theme');
                const supportDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && supportDarkMode)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            })();
        </script>
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
    <body class="font-sans antialiased text-base-content min-h-screen selection:bg-primary selection:text-primary-content"
          style="background-image: url('{{ asset('build/images/library-background.png') }}'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat;">

        {{-- Overlay to keep content readable --}}
        <div class="fixed inset-0 bg-base-100/80 backdrop-blur-[2px] -z-10 pointer-events-none"></div>

        @auth
        <div class="drawer lg:drawer-open">
            <input id="main-drawer" type="checkbox" class="drawer-toggle" />
            
            <div class="drawer-content flex flex-col min-h-screen">
                <!-- Top Navbar (Slim) -->
                <nav class="sticky top-0 z-40 h-16 w-full bg-base-100/80 backdrop-blur-md border-b border-base-200 transition-all duration-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Sidebar Toggle (Desktop) -->
                            <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)" class="hidden lg:flex btn btn-ghost btn-circle btn-sm opacity-50 hover:opacity-100">
                                <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                <svg x-show="sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path></svg>
                            </button>

                            <!-- Mobile Hamburguer -->
                            <div class="flex lg:hidden">
                                <label for="main-drawer" class="btn btn-ghost btn-circle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                </label>
                            </div>

                            <!-- Brand (Mobile Only) -->
                            <div class="flex lg:hidden flex-1 px-2">
                                <span class="text-xl font-bold text-primary">Mini-LMS</span>
                            </div>
                        </div>

                        <!-- Top Right Actions -->
                        <div class="flex items-center gap-2 ml-auto">
                            <!-- User context info (Desktop only) -->
                            <div class="hidden md:flex flex-col items-end mr-2">
                                <span class="text-xs font-bold">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] opacity-50 uppercase tracking-tighter">Library Admin</span>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Main Content Area -->
                <main class="flex-grow">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                        {{ $slot }}
                    </div>
                </main>

                <!-- SweetAlert2 Scripts -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        @if (session('success'))
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: "{{ session('success') }}",
                                confirmButtonColor: '#355872',
                                customClass: {
                                    popup: 'rounded-2xl border-none shadow-2xl',
                                    confirmButton: 'rounded-xl px-8'
                                }
                            });
                        @endif

                        @if (session('status'))
                            Toast.fire({
                                icon: 'info',
                                title: "{{ session('status') }}"
                            });
                        @endif

                        @if (session('error'))
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: "{{ session('error') }}",
                                confirmButtonColor: '#355872'
                            });
                        @endif

                        @if ($errors->any())
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: '<ul class="text-left text-sm space-y-1">@foreach ($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
                                confirmButtonColor: '#355872'
                            });
                        @endif
                    });
                </script>

                <!-- Footer -->
                <footer class="bg-base-200/50 backdrop-blur-sm border-t border-base-200 pt-16 pb-8">
                    <div class="container mx-auto px-4 text-center">
                        <p class="text-xs text-base-content/40">&copy; {{ date('Y') }} Mini-LMS. Smart Library Management.</p>
                    </div>
                </footer>
            </div>

                <!-- Profile Modal -->
                <div class="modal" :class="{ 'modal-open': showProfileModal }" style="background-color: rgba(0,0,0,0.5); z-index: 1000;">
                    <div class="modal-box max-w-4xl rounded-[2.5rem] p-0 border border-white/10 shadow-3xl overflow-hidden bg-base-100">
                        <div class="bg-base-200 p-8 border-b border-base-300 flex justify-between items-center bg-slate-900 text-white">
                            <div>
                                <h3 class="text-3xl font-bold tracking-tight">Account Settings</h3>
                                <p class="text-sm opacity-60 font-medium mt-1">Manage your professional profile and security</p>
                            </div>
                            <button @click="showProfileModal = false" class="btn btn-sm btn-circle btn-ghost text-white hover:bg-white/10">✕</button>
                        </div>
                        
                        <div class="p-8 space-y-12 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            <!-- Profile Info -->
                            <section class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">01</div>
                                    <h4 class="font-bold opacity-80 uppercase tracking-widest text-xs">Profile Information</h4>
                                </div>
                                <div class="bg-base-200/50 p-6 rounded-3xl border border-base-300">
                                    <div class="max-w-xl">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                </div>
                            </section>

                            <div class="divider opacity-10"></div>

                            <!-- Password -->
                            <section class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">02</div>
                                    <h4 class="font-bold opacity-80 uppercase tracking-widest text-xs">Security & Password</h4>
                                </div>
                                <div class="bg-base-200/50 p-6 rounded-3xl border border-base-300">
                                    <div class="max-w-xl">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </section>

                            <div class="divider opacity-10"></div>

                            <!-- Delete -->
                            <section class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-error/10 text-error flex items-center justify-center font-bold">03</div>
                                    <h4 class="font-bold opacity-80 uppercase tracking-widest text-xs text-error">Danger Zone</h4>
                                </div>
                                <div class="bg-error/5 p-6 rounded-3xl border border-error/10">
                                    <div class="max-w-xl">
                                        @include('profile.partials.delete-user-form')
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="p-6 bg-base-200/50 border-t border-base-300 flex justify-end">
                            <button @click="showProfileModal = false" class="btn btn-ghost rounded-xl px-10">Close Settings</button>
                        </div>
                    </div>
                </div>

            <!-- Sidebar -->
            <div class="drawer-side z-50 transition-all duration-300 overflow-hidden" :class="sidebarCollapsed ? 'w-16' : 'w-64'">
                <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <x-sidebar />
            </div>
        </div>
        @else
        <!-- Guest View -->
        <nav class="sticky top-0 z-50 transition-all duration-300 bg-base-100 border-b border-base-200">
            <div class="container mx-auto px-4">
                <div class="navbar h-20">
                    <div class="flex-1">
                        <a href="{{ route('home') }}" class="group flex items-center gap-3 no-underline">
                            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-primary-content shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold tracking-tight text-primary">Mini-LMS</span>
                        </a>
                    </div>
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
    </body>
</html>
