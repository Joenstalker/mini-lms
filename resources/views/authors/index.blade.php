<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-secondary text-primary-content rounded-2xl shadow-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold">✍️ Authors Collection</h1>
                <p class="text-lg opacity-90 mt-2">Browse and manage all book authors</p>
            </div>
            <a href="{{ route('authors.create') }}" class="btn btn-secondary btn-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4"></path>
                </svg>
                Add Author
            </a>
        </div>

        <!-- Authors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($authors as $author)
                <div class="card bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-blue-300">
                    <div class="card-body space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-12 font-bold">
                                    <span>{{ substr($author->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h2 class="card-title text-xl text-blue-900">{{ $author->name }}</h2>
                                <p class="text-xs opacity-60">Author Profile</p>
                            </div>
                        </div>
                        
                        @if ($author->bio)
                            <p class="text-sm text-blue-800 line-clamp-3 italic">{{ $author->bio }}</p>
                        @else
                            <p class="text-sm opacity-50 italic">No bio provided</p>
                        @endif
                        
                        <div class="divider my-2"></div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path></svg>
                                <span class="badge badge-primary badge-lg font-bold">{{ $author->books->count() }}</span>
                            </div>
                            <span class="text-xs text-base-content/60">book{{ $author->books->count() !== 1 ? 's' : '' }}</span>
                        </div>
                        
                        <div class="card-actions justify-end gap-2 mt-4">
                            <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-info btn-outline hover:btn-info transition-all duration-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                            </a>
                            <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-warning btn-outline hover:btn-warning transition-all duration-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                            </a>
                            <form method="POST" action="{{ route('authors.destroy', $author) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-error btn-outline hover:btn-error transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-gradient-to-br from-base-200 to-base-300 rounded-2xl shadow-lg p-12 text-center">
                        <div class="space-y-2">
                            <div class="text-5xl">✍️</div>
                            <p class="font-bold text-2xl text-base-content">No authors yet</p>
                            <p class="text-base-content/70"><a href="{{ route('authors.create') }}" class="link link-primary font-semibold">Create your first author</a> to get started</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <div class="join">
                {{ $authors->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
