<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | Mini-LMS</title>
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
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .error-code {
            font-size: 8rem;
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
    
    <div class="glass-container relative z-10 animate-in fade-in zoom-in duration-700">
        <div class="mb-6">
            <div class="w-20 h-20 bg-primary/20 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-primary/30 rotate-12">
                <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="error-code">404</h1>
        <h2 class="text-3xl font-extrabold text-white mb-4">Lost in the Stacks?</h2>
        <p class="text-white/60 text-lg mb-8 leading-relaxed">
            Oops! The page you're searching for seems to have been misplaced or archived.
        </p>
        
        <div class="flex flex-col gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary h-14 rounded-2xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-sky-500/30 hover:shadow-sky-500/50 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return to Library
            </a>
            <button onclick="window.history.back()" class="btn btn-ghost h-14 rounded-2xl font-bold uppercase tracking-widest text-xs text-white/40 hover:text-white hover:bg-white/5 transition-all">
                Go Back
            </button>
        </div>
    </div>

    {{-- Decorative Blur Particles --}}
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-sky-500/10 blur-[120px] rounded-full pointer-events-none"></div>
</body>
</html>