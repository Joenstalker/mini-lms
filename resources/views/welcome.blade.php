<x-app-layout>
    <div class="space-y-12">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-secondary text-primary-content rounded-2xl shadow-2xl p-12 md:p-16 relative overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48"></div>
            
            <div class="relative z-10">
                <h1 class="text-5xl md:text-6xl font-bold mb-4 leading-tight">Welcome to Mini-LMS</h1>
                <p class="text-xl md:text-2xl opacity-90 mb-8 font-light">Your modern, intelligent library management system</p>
                <div class="flex gap-4 flex-wrap">
                    <a href="{{ route('books.index') }}" class="btn btn-lg btn-secondary shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                        Browse Books Catalog
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline btn-primary-content hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-lg btn-outline btn-primary-content hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            Sign In to Manage
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat bg-base-200 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="stat-figure text-primary text-4xl">📚</div>
                <div class="stat-title text-base-content/70 font-semibold">Total Books</div>
                <div class="stat-value text-primary text-3xl">{{ \App\Models\Book::count() }}</div>
                <div class="stat-desc text-base-content/60">Available in catalog</div>
            </div>
            
            <div class="stat bg-base-200 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="stat-figure text-success text-4xl">👥</div>
                <div class="stat-title text-base-content/70 font-semibold">Registered Students</div>
                <div class="stat-value text-success text-3xl">{{ \App\Models\Student::count() }}</div>
                <div class="stat-desc text-base-content/60">Active library members</div>
            </div>
            
            <div class="stat bg-base-200 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="stat-figure text-warning text-4xl">✍️</div>
                <div class="stat-title text-base-content/70 font-semibold">Total Authors</div>
                <div class="stat-value text-warning text-3xl">{{ \App\Models\Author::count() }}</div>
                <div class="stat-desc text-base-content/60">Contributing authors</div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="space-y-4">
            <h2 class="text-4xl font-bold text-base-content">Why Choose Mini-LMS?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-blue-300">
                    <div class="card-body">
                        <div class="text-5xl mb-4">📖</div>
                        <h3 class="card-title text-xl font-bold text-blue-900">Smart Book Management</h3>
                        <p class="text-blue-800">Browse hundreds of books with real-time availability tracking, multiple authors support, and detailed publication information.</p>
                        <div class="mt-4">
                            <div class="badge badge-lg badge-outline badge-primary">Advanced Search</div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-green-300">
                    <div class="card-body">
                        <div class="text-5xl mb-4">👨‍🎓</div>
                        <h3 class="card-title text-xl font-bold text-green-900">Student Profiles</h3>
                        <p class="text-green-800">Comprehensive student management with borrowing history, activity tracking, and personalized library experience for each member.</p>
                        <div class="mt-4">
                            <div class="badge badge-lg badge-outline badge-success">Track Activity</div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-purple-300">
                    <div class="card-body">
                        <div class="text-5xl mb-4">⚡</div>
                        <h3 class="card-title text-xl font-bold text-purple-900">Automatic Fine System</h3>
                        <p class="text-purple-800">Smart fine calculation (₱10/day per book) with automatic tracking of overdue books and real-time fine management.</p>
                        <div class="mt-4">
                            <div class="badge badge-lg badge-outline badge-secondary">Intelligent Calc</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Books Section -->
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-4xl font-bold text-base-content">Featured Books</h2>
                <a href="{{ route('books.index') }}" class="link link-primary font-semibold text-lg hover:gap-2 transition-all inline-flex items-center gap-1">
                    View All Books
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gradient-to-r from-primary to-primary/80 text-primary-content">
                        <tr>
                            <th class="rounded-tl-lg">Title & ISBN</th>
                            <th>Authors</th>
                            <th class="text-center">Availability</th>
                            <th class="rounded-tr-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (\App\Models\Book::with('authors')->latest()->limit(5)->get() as $book)
                            <tr class="hover:bg-primary/10 transition-colors duration-200">
                                <td>
                                    <div>
                                        <div class="font-bold text-base text-base-content">{{ $book->title }}</div>
                                        <div class="text-sm opacity-70 font-mono">ISBN: {{ $book->isbn }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($book->authors as $author)
                                            <span class="badge badge-primary badge-outline">{{ $author->name }}</span>
                                        @empty
                                            <span class="text-xs opacity-50 italic">No authors listed</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($book->available_quantity > 0)
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="badge badge-success badge-lg font-bold">{{ $book->available_quantity }} Available</span>
                                            <div class="progress progress-success w-24 h-2">
                                                <div class="progress-value bg-success" style="width: {{ ($book->available_quantity / $book->total_quantity) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-error badge-lg font-bold">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-primary btn-outline hover:btn-primary transition-all duration-300">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12">
                                    <div class="space-y-2">
                                        <div class="text-2xl">📚</div>
                                        <p class="text-base-content/60 font-semibold">No books available yet</p>
                                        <p class="text-sm text-base-content/50">Start by adding some books to your library</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Call to Action -->
        @guest
            <div class="bg-gradient-to-r from-secondary via-secondary/90 to-accent text-secondary-content rounded-2xl shadow-2xl p-8 md:p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 1200 600" xmlns="http://www.w3.org/2000/svg">
                        <pattern id="dots" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse">
                            <circle cx="25" cy="25" r="2" fill="white" />
                        </pattern>
                        <rect width="1200" height="600" fill="url(#dots)" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-4xl font-bold mb-4">Ready to manage your library?</h3>
                    <p class="text-lg opacity-90 mb-8 max-w-2xl mx-auto">Create an account to access the full power of Mini-LMS and start managing your book inventory, student profiles, and borrowing transactions.</p>
                    <div class="flex gap-4 justify-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-lg btn-primary hover:shadow-lg transition-all duration-300 hover:scale-105">Get Started</a>
                        <a href="{{ route('login') }}" class="btn btn-lg btn-outline btn-secondary-content hover:bg-white/20 transition-all duration-300 hover:scale-105">Sign In</a>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</x-app-layout>
