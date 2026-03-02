<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-image: url('{{ asset('images/login-page-background-photo.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .glass-container {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 4rem;
            text-align: center;
            max-width: 32rem;
            width: 90%;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            background: linear-gradient(to bottom right, #38bdf8, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
            filter: drop-shadow(0 10px 10px rgba(14, 165, 233, 0.3));
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">
    <div class="absolute inset-0 bg-black/50 z-0"></div>
    
    <div class="glass-container relative z-10">
        <div class="mb-4">
            <h1 class="error-code">@yield('code')</h1>
            <h2 class="text-2xl font-extrabold text-white mb-2">@yield('title')</h2>
            <p class="text-white/60 text-lg mb-8">
                @yield('message')
            </p>
        </div>
        
        <div class="flex flex-col gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary h-14 rounded-2xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-sky-500/30 hover:shadow-sky-500/50 transition-all flex items-center justify-center gap-2">
                Return to Library
            </a>
        </div>
    </div>

    {{-- Decorative Blur Particles --}}
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-sky-500/10 blur-[120px] rounded-full pointer-events-none"></div>
</body>
</html>
