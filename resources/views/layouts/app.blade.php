<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mini LMS') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen">
    <nav class="bg-indigo-700 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-white font-bold text-lg tracking-tight">📚 Mini LMS</a>
                    <div class="hidden md:flex space-x-1">
                        <a href="{{ route('dashboard') }}" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-800 text-white' : '' }}">Dashboard</a>
                        <a href="{{ route('books.index') }}" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('books.*') ? 'bg-indigo-800 text-white' : '' }}">Books</a>
                        <a href="{{ route('authors.index') }}" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('authors.*') ? 'bg-indigo-800 text-white' : '' }}">Authors</a>
                        <a href="{{ route('students.index') }}" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('students.*') ? 'bg-indigo-800 text-white' : '' }}">Students</a>
                        <a href="{{ route('borrowings.index') }}" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('borrowings.*') ? 'bg-indigo-800 text-white' : '' }}">Borrowings</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-indigo-200 text-sm hidden sm:block">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-indigo-200 hover:text-white text-sm bg-indigo-800 hover:bg-indigo-900 px-3 py-1.5 rounded-md transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg">❌ {{ session('error') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
