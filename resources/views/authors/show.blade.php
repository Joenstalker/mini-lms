<x-app-layout>
    <div class="space-y-6">
        <div class="glass text-white rounded-[2rem] p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10 mb-6 font-medium">
            <div>
                <h1 class="text-4xl font-bold">{{ $author->name }}</h1>
                <p class="text-lg text-white/60 mt-2">Author Profile</p>
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
            <div class="lg:col-span-2 glass shadow-xl p-8 rounded-[2rem] border border-white/10 text-white">
                <h2 class="text-2xl font-bold mb-6">About</h2>
                @if ($author->bio)
                    <p class="text-white/80 leading-relaxed text-lg">{{ $author->bio }}</p>
                @else
                    <p class="text-white/40 italic">No bio available</p>
                @endif
            </div>

            <div class="glass shadow-xl p-8 rounded-[2rem] border border-white/10 text-white">
                <h2 class="text-2xl font-bold mb-6">Statistics</h2>
                <div class="bg-white/5 p-6 rounded-2xl border border-white/10 text-center">
                    <div class="text-5xl font-black text-primary">{{ $author->books->count() }}</div>
                    <div class="text-[10px] uppercase tracking-widest font-black text-white/40 mt-2">Books in Library</div>
                </div>
            </div>
        </div>

        <div class="glass shadow-xl p-8 rounded-[2rem] border border-white/10 text-white">
            <h2 class="text-2xl font-bold mb-8">Books Catalog by {{ $author->name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($author->books as $book)
                    <a href="{{ route('books.index') }}" class="card glass-card hover:shadow-2xl transition-all duration-500 border-white/10 text-white">
                        <div class="card-body p-6">
                            <h3 class="card-title text-lg font-bold">{{ $book->title }}</h3>
                            <div class="badge badge-primary badge-outline mt-3 font-black text-[10px] tracking-tight p-3 border-white/20 text-white">{{ $book->available_quantity }}/{{ $book->total_quantity }} Available</div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-base-content/60">No books by this author yet</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
