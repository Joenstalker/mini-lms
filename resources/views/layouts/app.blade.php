<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mini Library Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-base-100 via-base-100 to-primary/5">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="navbar bg-gradient-to-r from-primary via-primary to-primary/90 text-primary-content shadow-2xl sticky top-0 z-40 backdrop-blur-sm bg-opacity-95">
                <div class="flex-1">
                    <a href="{{ route('home') }}" class="btn btn-ghost text-2xl font-bold hover:bg-white hover:bg-opacity-10 transition-all duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Mini-LMS
                    </a>
                </div>

                <div class="flex-none gap-2">
                    {{-- Navigation Items --}}
                    @auth
                        <div class="dropdown dropdown-end">
                            <button class="btn btn-ghost gap-2 hover:bg-white hover:bg-opacity-10 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path>
                                </svg>
                                <span class="hidden md:inline">Manage</span>
                            </button>
                            <ul class="dropdown-content z-[1] menu p-2 shadow-xl bg-base-100 rounded-xl w-52 text-base-content">
                                <li><a href="{{ route('books.index') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Books</a></li>
                                <li><a href="{{ route('authors.index') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Authors</a></li>
                                <li><a href="{{ route('students.index') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Students</a></li>
                                <li><a href="{{ route('borrow-transactions.index') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Transactions</a></li>
                                <li><a href="{{ route('borrow-transactions.overdue') }}" class="hover:bg-error hover:text-error-content transition-colors">Overdue Books</a></li>
                            </ul>
                        </div>

                        <div class="dropdown dropdown-end">
                            <button class="btn btn-ghost gap-2 hover:bg-white hover:bg-opacity-10 transition-all duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-content z-[1] menu p-2 shadow-xl bg-base-100 rounded-xl w-52 text-base-content">
                                <li><a href="{{ route('dashboard') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Dashboard</a></li>
                                <li><a href="{{ route('profile.edit') }}" class="hover:bg-primary hover:text-primary-content transition-colors">Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a onclick="this.closest('form').submit()" class="hover:bg-error hover:text-error-content transition-colors">Logout</a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline text-primary-content border-primary-content hover:bg-white hover:text-primary transition-all duration-300">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-secondary hover:shadow-lg transition-all duration-300">Register</a>
                    @endguest
                </div>
            </nav>

            <!-- Main Content -->
            <main class="flex-grow container mx-auto px-4 py-8 w-full max-w-7xl">
                @if ($errors->any())
                    <div class="alert alert-error mb-4 shadow-lg">
                        <div>
                            <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-bold">Error!</h3>
                                <div class="text-sm">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success mb-4 shadow-lg">
                        <div>
                            <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-base-200 text-base-content py-8 mt-12">
                <div class="container mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <div>
                            <h5 class="font-bold text-lg mb-2">Mini-LMS</h5>
                            <p class="text-sm">A modern library management solution</p>
                        </div>
                        <div>
                            <h5 class="font-bold mb-2">Features</h5>
                            <ul class="text-sm space-y-1">
                                <li><a href="" class="link link-hover">Student Management</a></li>
                                <li><a href="" class="link link-hover">Book Catalog</a></li>
                                <li><a href="" class="link link-hover">Borrowing System</a></li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-bold mb-2">Technology</h5>
                            <ul class="text-sm space-y-1">
                                <li>Laravel 12</li>
                                <li>Tailwind CSS</li>
                                <li>DaisyUI</li>
                            </ul>
                        </div>
                    </div>
                    <div class="divider my-4"></div>
                    <div class="text-sm text-center">
                        <p>&copy; 2026 Mini-LMS. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
