<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">{{ $author->name }}</h1>
                <p class="text-base-content/60 mt-2">Author Profile</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('authors.edit', $author) }}" class="btn btn-outline">Edit</a>
                <form method="POST" action="{{ route('authors.destroy', $author) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-error">Delete</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Author Info -->
            <div class="lg:col-span-2 bg-base-200 rounded-lg shadow-md p-8">
                <h2 class="text-xl font-bold mb-6">About</h2>
                @if ($author->bio)
                    <p class="opacity-70 leading-relaxed">{{ $author->bio }}</p>
                @else
                    <p class="opacity-50">No bio available</p>
                @endif
            </div>

            <!-- Stats -->
            <div class="bg-base-200 rounded-lg shadow-md p-8">
                <h2 class="text-xl font-bold mb-6">Statistics</h2>
                <div>
                    <div class="text-3xl font-bold text-primary">{{ $author->books->count() }}</div>
                    <div class="text-sm opacity-50">Books in Library</div>
                </div>
            </div>
        </div>

        <!-- Books -->
        <div class="bg-base-200 rounded-lg shadow-md p-8">
            <h2 class="text-xl font-bold mb-6">Books by {{ $author->name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($author->books as $book)
                    <a href="{{ route('books.show', $book) }}" class="card bg-base-300 shadow-md hover:shadow-lg transition">
                        <div class="card-body">
                            <h3 class="card-title text-base">{{ $book->title }}</h3>
                            <div class="badge badge-ghost mt-2">{{ $book->available_quantity }}/{{ $book->total_quantity }} available</div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-base-content/60">No books by this author yet</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
