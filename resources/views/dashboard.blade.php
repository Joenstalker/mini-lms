<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-secondary text-primary-content rounded-2xl shadow-2xl p-8 md:p-12">
            <h1 class="text-5xl font-bold mb-2">Dashboard</h1>
            <p class="text-lg opacity-90">Welcome back, <span class="font-semibold">{{ Auth::user()->name }}</span>! Here's your library overview.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-blue-500">
                <div class="stat-figure text-blue-600 text-4xl">📚</div>
                <div class="stat-title text-base-content/70 font-semibold">Total Books</div>
                <div class="stat-value text-blue-600 text-3xl">{{ \App\Models\Book::count() }}</div>
                <div class="stat-desc text-blue-600/70">In your collection</div>
            </div>

            <div class="stat bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-green-500">
                <div class="stat-figure text-green-600 text-4xl">👥</div>
                <div class="stat-title text-base-content/70 font-semibold">Total Students</div>
                <div class="stat-value text-green-600 text-3xl">{{ \App\Models\Student::count() }}</div>
                <div class="stat-desc text-green-600/70">Active members</div>
            </div>

            <div class="stat bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-purple-500">
                <div class="stat-figure text-purple-600 text-4xl">📤</div>
                <div class="stat-title text-base-content/70 font-semibold">Active Borrows</div>
                <div class="stat-value text-purple-600 text-3xl">{{ \App\Models\BorrowTransaction::whereIn('status', ['borrowed', 'partially_returned'])->count() }}</div>
                <div class="stat-desc text-purple-600/70">Currently out</div>
            </div>

            <div class="stat bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-red-500">
                <div class="stat-figure text-red-600 text-4xl">⚠️</div>
                <div class="stat-title text-base-content/70 font-semibold">Overdue Books</div>
                <div class="stat-value text-red-600 text-3xl">{{ \App\Models\BorrowTransaction::whereIn('status', ['borrowed', 'partially_returned'])->where('due_date', '<', now())->count() }}</div>
                <div class="stat-desc text-red-600/70">Need attention</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-base-200 to-base-300 rounded-2xl shadow-lg p-8 border border-base-300">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Quick Actions
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('books.index') }}" class="btn btn-primary shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                    </svg>
                    <span class="hidden sm:inline">All Books</span>
                </a>
                <a href="{{ route('books.create') }}" class="btn btn-success shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Add Book</span>
                </a>
                <a href="{{ route('borrow-transactions.create') }}" class="btn btn-accent shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">New Borrow</span>
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-info shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">New Student</span>
                </a>
                <a href="{{ route('borrow-transactions.overdue') }}" class="btn btn-error shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="hidden sm:inline">Overdue</span>
                </a>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-gradient-to-br from-base-100 to-base-200 rounded-2xl shadow-lg p-8 border border-base-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H2a2 2 0 012-2zm0 0a2 2 0 012-2 1 1 0 000 2h8a1 1 0 000-2 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm6 12a1 1 0 100-2h-2a1 1 0 100 2h2z" clip-rule="evenodd"></path>
                    </svg>
                    Recent Transactions
                </h2>
                <a href="{{ route('borrow-transactions.index') }}" class="link link-primary font-semibold">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gradient-to-r from-primary/10 to-secondary/10">
                        <tr class="hover:bg-primary/5">
                            <th class="font-bold">Student</th>
                            <th class="font-bold">Book</th>
                            <th class="font-bold">Borrow Date</th>
                            <th class="font-bold">Due Date</th>
                            <th class="font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (\App\Models\BorrowTransaction::with('student', 'book')->latest()->limit(5)->get() as $transaction)
                            <tr class="hover:bg-primary/5 transition-colors">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full w-8">
                                                <span class="text-sm">{{ substr($transaction->student->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="font-semibold">{{ $transaction->student->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-semibold text-base-content">{{ Str::limit($transaction->book->title, 30) }}</div>
                                    <div class="text-xs text-base-content/60">ISBN: {{ $transaction->book->isbn }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $transaction->borrow_date->format('M d') }}</div>
                                    <div class="text-xs text-base-content/60">{{ $transaction->borrow_date->format('Y') }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold {{ $transaction->due_date < now() ? 'text-error' : 'text-success' }}">{{ $transaction->due_date->format('M d') }}</div>
                                    <div class="text-xs text-base-content/60">{{ $transaction->due_date->format('Y') }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($transaction->status === 'borrowed')
                                        <span class="badge badge-lg badge-info gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 13a3 3 0 105 5H5v-5z"></path></svg>
                                            Borrowed
                                        </span>
                                    @elseif ($transaction->status === 'partially_returned')
                                        <span class="badge badge-lg badge-warning gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path></svg>
                                            Partial
                                        </span>
                                    @else
                                        <span class="badge badge-lg badge-success gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                                            Returned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-base-content/60">
                                    <div class="space-y-2">
                                        <div class="text-3xl">📭</div>
                                        <p class="font-semibold">No transactions yet</p>
                                        <p class="text-sm">Start adding borrowing records to see them here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
