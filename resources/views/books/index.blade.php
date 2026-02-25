<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-secondary text-primary-content rounded-2xl shadow-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold">📚 Books Catalog</h1>
                <p class="text-lg opacity-90 mt-2">Explore and manage your library collection</p>
            </div>
            @auth
                <a href="{{ route('books.create') }}" class="btn btn-secondary btn-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Book
                </a>
            @endauth
        </div>

        <!-- Books Table -->
        <div class="bg-gradient-to-br from-base-100 to-base-200 rounded-2xl shadow-lg border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gradient-to-r from-primary to-primary/80 text-primary-content sticky top-0">
                        <tr>
                            <th class="text-base font-bold rounded-tl-lg">Title & Publisher</th>
                            <th class="font-bold">Authors</th>
                            <th class="font-bold">ISBN</th>
                            <th class="text-center font-bold">Available</th>
                            <th class="text-center font-bold">Total</th>
                            <th class="font-bold rounded-tr-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr class="hover:bg-primary/10 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-base text-base-content">{{ $book->title }}</div>
                                        <div class="text-sm opacity-70 italic">{{ $book->publisher ?? 'Independent' }}</div>
                                        <div class="text-xs opacity-50">Published: {{ $book->published_year ?? 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($book->authors as $author)
                                            <a href="{{ route('authors.show', $author) }}" class="badge badge-primary badge-outline hover:badge-primary transition-all duration-300">{{ $author->name }}</a>
                                        @empty
                                            <span class="text-xs opacity-50 italic">No authors</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-sm font-semibold">{{ $book->isbn }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($book->available_quantity > 0)
                                        <span class="badge badge-success badge-lg font-bold">{{ $book->available_quantity }}</span>
                                    @else
                                        <span class="badge badge-error badge-lg font-bold">Out</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-outline badge-lg">{{ $book->total_quantity }}</span>
                                </td>
                                <td>
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-info btn-outline hover:btn-info transition-all duration-300" title="View Details">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                        @auth
                                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-warning btn-outline hover:btn-warning transition-all duration-300" title="Edit">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('books.destroy', $book) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-error btn-outline hover:btn-error transition-all duration-300" title="Delete">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16 text-base-content/60">
                                    <div class="space-y-2">
                                        <div class="text-4xl">📭</div>
                                        <p class="font-semibold text-lg">No books found</p>
                                        @auth
                                            <p class="text-sm">Start by <a href="{{ route('books.create') }}" class="link link-primary font-semibold">adding your first book</a></p>
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <div class="join">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
