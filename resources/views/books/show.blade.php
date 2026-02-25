<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">{{ $book->title }}</h1>
                <p class="text-base-content/60 mt-2">Book Details & Information</p>
            </div>
            @auth
                <div class="flex gap-2">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-outline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-error">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info Card -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold mb-4">Book Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm opacity-50 mb-1">Publisher</div>
                            <div class="font-semibold">{{ $book->publisher ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-sm opacity-50 mb-1">Published Year</div>
                            <div class="font-semibold">{{ $book->published_year ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <div class="text-sm opacity-50 mb-1">Total Copies</div>
                            <div class="font-semibold">{{ $book->total_quantity }}</div>
                        </div>
                    </div>

                    @if ($book->description)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="font-bold mb-2">Description</h3>
                            <p class="opacity-70">{{ $book->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Authors Card -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold mb-4">Authors</h2>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($book->authors as $author)
                            <a href="{{ route('authors.show', $author) }}" class="badge badge-lg badge-primary gap-2 cursor-pointer hover:badge-secondary">
                                {{ $author->name }}
                            </a>
                        @empty
                            <p class="opacity-50">No authors associated</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Availability Card -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold mb-4">Availability</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm opacity-50 mb-2">Available Copies</div>
                            <div class="text-4xl font-bold {{ $book->available_quantity > 0 ? 'text-success' : 'text-error' }}">
                                {{ $book->available_quantity }}
                            </div>
                        </div>
                        <div class="divider my-2"></div>
                        <div>
                            <div class="text-sm opacity-50 mb-2">Total Copies</div>
                            <div class="text-2xl font-bold">{{ $book->total_quantity }}</div>
                        </div>
                        <div class="progress" role="progressbar">
                            <div class="progress-value" style="width: {{ ($book->available_quantity / $book->total_quantity) * 100 }}%"></div>
                        </div>
                        <div class="text-xs opacity-50">{{ round(($book->available_quantity / $book->total_quantity) * 100) }}% available</div>
                    </div>
                    
                    @guest
                        @if ($book->available_quantity > 0)
                            <a href="{{ route('borrow-transactions.create', ['book_id' => $book->id]) }}" class="btn btn-primary w-full mt-4 shadow-lg hover:shadow-primary/20 transition-all font-bold">
                                Borrow This Book
                            </a>
                        @else
                            <button disabled class="btn w-full mt-4 opacity-50 font-bold">No Copies Available</button>
                        @endif
                    @endguest
                </div>

                <!-- Recent Borrows Card -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-lg font-bold mb-4">Recent Activity</h2>
                    <div class="text-sm space-y-2 opacity-70">
                        @forelse ($book->borrowTransactions()->latest()->limit(5)->get() as $transaction)
                            <div class="pb-2 border-b">
                                <div class="font-semibold">{{ $transaction->student->name }}</div>
                                <div class="text-xs opacity-50">{{ $transaction->borrow_date->format('M d, Y') }}</div>
                            </div>
                        @empty
                            <p>No borrow history yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
