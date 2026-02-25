<x-app-layout>
    <div class="space-y-24 py-12">
        <!-- Hero Section -->
        <section class="relative">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-semibold tracking-wide animate-bounce">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                        </span>
                        New: Digital Library Access
                    </div>
                    <h1 class="text-6xl md:text-7xl font-extrabold leading-[1.1] tracking-tight">
                        Revolutionize Your <br>
                        <span class="gradient-text">Library Experience</span>
                    </h1>
                    <p class="text-xl text-base-content/60 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Mini-LMS provides a modern, intuitive platform to manage, track, and explore your book collection with ease and intelligence.
                    </p>
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg rounded-2xl px-8 shadow-xl shadow-primary/30 hover:scale-105 transition-all">
                            Explore Catalog
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"></path></svg>
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline btn-lg rounded-2xl px-8 hover:bg-base-200">
                                Get Started
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-lg rounded-2xl px-8 hover:bg-base-200">
                                Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
                
                <div class="flex-1 relative w-full max-w-lg lg:max-w-none">
                    <div class="aspect-square relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-secondary/20 rounded-full blur-[80px] animate-pulse"></div>
                        <div class="relative glass-card p-1 translate-y-6">
                            <div class="bg-base-100 rounded-2xl overflow-hidden shadow-2xl">
                                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80&w=800" alt="Library" class="w-full h-auto grayscale-[0.2] hover:grayscale-0 transition-all duration-700">
                                <div class="p-8 space-y-4">
                                    <div class="flex gap-2">
                                        <div class="h-2 w-16 bg-primary/20 rounded-full"></div>
                                        <div class="h-2 w-8 bg-secondary/20 rounded-full"></div>
                                    </div>
                                    <div class="h-4 w-3/4 bg-base-300 rounded-full"></div>
                                    <div class="h-4 w-1/2 bg-base-200 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-12 -left-12 glass-card p-6 shadow-2xl animate-float">
                            <div class="text-4xl">📚</div>
                            <div class="mt-2 text-xl font-bold">12k+ Books</div>
                            <div class="text-xs opacity-50">Cataloged Daily</div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 glass-card p-6 shadow-2xl animate-float" style="animation-delay: -2s;">
                            <div class="text-4xl">👥</div>
                            <div class="mt-2 text-xl font-bold">5k+ Users</div>
                            <div class="text-xs opacity-50">Active Readers</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="text-base-content/40 text-sm font-bold uppercase tracking-widest mb-4">Total Inventory</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-extrabold">{{ \App\Models\Book::count() }}</span>
                    <span class="text-primary text-lg font-bold mb-1">Books</span>
                </div>
                <div class="w-full h-1 bg-primary/10 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-primary w-2/3 group-hover:w-full transition-all duration-1000"></div>
                </div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="text-base-content/40 text-sm font-bold uppercase tracking-widest mb-4">Active Members</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-extrabold">{{ \App\Models\Student::count() }}</span>
                    <span class="text-secondary text-lg font-bold mb-1">Users</span>
                </div>
                <div class="w-full h-1 bg-secondary/10 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-secondary w-1/2 group-hover:w-full transition-all duration-1000"></div>
                </div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="text-base-content/40 text-sm font-bold uppercase tracking-widest mb-4">Writers</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-extrabold">{{ \App\Models\Author::count() }}</span>
                    <span class="text-accent text-lg font-bold mb-1">Authors</span>
                </div>
                <div class="w-full h-1 bg-accent/10 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-accent w-3/4 group-hover:w-full transition-all duration-1000"></div>
                </div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="text-base-content/40 text-sm font-bold uppercase tracking-widest mb-4">Pending Loans</div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-extrabold">{{ \App\Models\BorrowTransaction::whereNull('returned_at')->count() }}</span>
                    <span class="text-error text-lg font-bold mb-1">Active</span>
                </div>
                <div class="w-full h-1 bg-error/10 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-error w-1/3 group-hover:w-full transition-all duration-1000"></div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="space-y-12">
            <div class="text-center space-y-4">
                <h2 class="text-4xl font-bold">Comprehensive <span class="text-primary">Features</span></h2>
                <p class="text-base-content/60 max-w-xl mx-auto">Everything you need to run a modern library, designed for efficiency and ease of use.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-10 hover:bg-primary hover:text-primary-content transition-all duration-500 group">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-3xl mb-8 group-hover:bg-white/20">📖</div>
                    <h3 class="text-2xl font-bold mb-4">Smart Inventory</h3>
                    <p class="opacity-70 leading-relaxed">Advanced tracking of books with real-time availability, multiple authors support, and ISBN integration.</p>
                </div>

                <div class="glass-card p-10 hover:bg-secondary hover:text-secondary-content transition-all duration-500 group">
                    <div class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center text-3xl mb-8 group-hover:bg-white/20">⚡</div>
                    <h3 class="text-2xl font-bold mb-4">Automated Loans</h3>
                    <p class="opacity-70 leading-relaxed">Simplified borrowing process with automated fine calculation (₱10/day) and overdue notifications.</p>
                </div>

                <div class="glass-card p-10 hover:bg-neutral hover:text-neutral-content transition-all duration-500 group">
                    <div class="w-14 h-14 rounded-2xl bg-neutral/10 flex items-center justify-center text-3xl mb-8 group-hover:bg-white/20">📊</div>
                    <h3 class="text-2xl font-bold mb-4">Admin Insights</h3>
                    <p class="opacity-70 leading-relaxed">Comprehensive dashboard with statistics on books, students, and transaction history for better management.</p>
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
            <section class="relative rounded-[2.5rem] overflow-hidden bg-neutral text-neutral-content p-12 md:p-24 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 via-transparent to-secondary/20"></div>
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
