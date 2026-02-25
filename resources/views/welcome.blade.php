<x-app-layout>
    <div class="space-y-24 py-12">
        <!-- Hero Section -->
        <section class="relative">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/5 text-primary text-xs font-bold tracking-widest uppercase">
                        Digital Library Access
                    </div>
                    <h1 class="text-6xl md:text-7xl font-black leading-tight tracking-tighter text-base-content">
                        Modernize Your <br>
                        <span class="text-primary">Library Flow</span>
                    </h1>
                    <p class="text-xl text-base-content/50 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                        Mini-LMS provides a minimalist, professional platform to manage, track, and explore your book collection with ease.
                    </p>
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg rounded-xl px-10 shadow-md">
                            Explore Catalog
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline btn-lg rounded-xl px-10">
                                Get Started
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-lg rounded-xl px-10">
                                Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
                
                <div class="flex-1 relative w-full max-w-lg lg:max-w-none">
                    <div class="aspect-square relative flex items-center justify-center">
                        <div class="relative bg-base-100 rounded-[2.5rem] p-4 border border-base-200 shadow-premium">
                            <div class="bg-base-200 rounded-[2rem] overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80&w=800" alt="Library" class="w-full h-auto grayscale transition-all duration-700 hover:grayscale-0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-base-200 p-8 rounded-3xl border border-base-300">
                <div class="text-[10px] font-bold uppercase tracking-widest mb-4 opacity-40">Total Inventory</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black">{{ \App\Models\Book::count() }}</span>
                    <span class="text-primary text-sm font-bold mb-1">Books</span>
                </div>
            </div>

            <div class="bg-base-200 p-8 rounded-3xl border border-base-300">
                <div class="text-[10px] font-bold uppercase tracking-widest mb-4 opacity-40">Active Members</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black">{{ \App\Models\Student::count() }}</span>
                    <span class="text-primary text-sm font-bold mb-1">Users</span>
                </div>
            </div>

            <div class="bg-base-200 p-8 rounded-3xl border border-base-300">
                <div class="text-[10px] font-bold uppercase tracking-widest mb-4 opacity-40">Writers</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black">{{ \App\Models\Author::count() }}</span>
                    <span class="text-primary text-sm font-bold mb-1">Authors</span>
                </div>
            </div>

            <div class="bg-base-200 p-8 rounded-3xl border border-base-300">
                <div class="text-[10px] font-bold uppercase tracking-widest mb-4 opacity-40">Active Loans</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black">{{ \App\Models\BorrowTransaction::whereNull('returned_at')->count() }}</span>
                    <span class="text-error text-sm font-bold mb-1">Loans</span>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="space-y-12">
            <div class="text-center space-y-4">
                <h2 class="text-4xl font-black">Core Features</h2>
                <p class="text-base-content/50 max-w-xl mx-auto font-medium">Professional grade library management without the clutter.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-base-100 p-10 rounded-[2rem] border border-base-200 hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center text-2xl mb-8">📖</div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Smart Inventory</h3>
                    <p class="opacity-50 leading-relaxed text-sm font-medium">Advanced tracking of books with real-time availability and ISSN integration.</p>
                </div>

                <div class="bg-base-100 p-10 rounded-[2rem] border border-base-200 hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center text-2xl mb-8">⚡</div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Automated Flow</h3>
                    <p class="opacity-50 leading-relaxed text-sm font-medium">Simplified borrowing process and automated fine calculation built for efficiency.</p>
                </div>

                <div class="bg-base-100 p-10 rounded-[2rem] border border-base-200 hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center text-2xl mb-8">📊</div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Admin Insights</h3>
                    <p class="opacity-50 leading-relaxed text-sm font-medium">Clean, professional dashboard with real-time analytics for better management.</p>
                </div>
            </div>
        </section>

        <!-- Featured Books -->
        <section class="space-y-12">
            <div class="flex justify-between items-end">
                <div class="space-y-2">
                    <h2 class="text-4xl font-bold">Latest Additions</h2>
                    <p class="text-base-content/60">Discover our newest additions to the catalog.</p>
                </div>
                <a href="{{ route('books.index') }}" class="btn btn-ghost hover:bg-primary/10 text-primary font-bold">
                    View Catalog
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-lg">
                        <thead class="bg-base-200/50">
                            <tr>
                                <th class="py-6">Book Details</th>
                                <th>Authors</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (\App\Models\Book::with('authors')->latest()->limit(5)->get() as $book)
                                <tr class="hover:bg-primary/5 transition-colors border-b border-base-200/50">
                                    <td class="py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-16 bg-base-300 rounded shadow-sm flex items-center justify-center text-xs opacity-50">IMG</div>
                                            <div>
                                                <div class="font-bold text-lg">{{ $book->title }}</div>
                                                <div class="text-sm opacity-50 font-mono">ISBN: {{ $book->isbn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($book->authors as $author)
                                                <span class="badge badge-outline border-base-300">{{ $author->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($book->available_quantity > 0)
                                            <div class="badge badge-success badge-outline gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-success"></div>
                                                {{ $book->available_quantity }} Available
                                            </div>
                                        @else
                                            <div class="badge badge-error badge-outline gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-error"></div>
                                                Out of Stock
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-primary btn-sm rounded-lg px-6">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-20">
                                        <div class="opacity-20 text-6xl mb-4">📚</div>
                                        <div class="text-xl font-medium opacity-50">Your library is currently empty.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- CTA -->
        @guest
            <section class="relative rounded-[2.5rem] overflow-hidden bg-slate-900 text-white p-12 md:p-24 shadow-2xl border border-white/5">
                <div class="absolute inset-0 bg-primary/5"></div>
                <div class="relative z-10 text-center space-y-8 max-w-3xl mx-auto">
                    <h2 class="text-4xl md:text-5xl font-extrabold">Ready to modernize your library?</h2>
                    <p class="text-lg opacity-70 leading-relaxed">Join hundreds of library managers who have already simplified their daily operations with Mini-LMS.</p>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-2xl px-12 shadow-xl shadow-primary/40">Register Now</a>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-lg rounded-2xl px-12 border-white/20 hover:bg-white/10">Sign In</a>
                    </div>
                </div>
            </section>
        @endguest
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</x-app-layout>
