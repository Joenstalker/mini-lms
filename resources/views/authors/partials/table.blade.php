<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($authors as $author)
        <div class="card glass-card hover:shadow-2xl transition-all duration-500 hover:border-primary/40 group/card">
            <div class="card-body space-y-4">
                <div class="flex items-start gap-3">
                    <div class="avatar shadow-xl border-2 border-white/20 rounded-xl overflow-hidden hover:scale-110 transition-transform duration-500">
                        <div class="w-14 h-14 bg-primary/20 flex items-center justify-center">
                            @if($author->profile_image)
                                <img src="{{ $author->profile_image }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-white font-extrabold text-xl">{{ substr($author->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-white group-hover/card:text-primary transition-colors leading-tight">{{ $author->name }}</h3>
                        <p class="text-xs text-white/50 font-medium tracking-wide">Professional Author</p>
                    </div>
                </div>
                
                <div class="bg-base-100/10 p-3 rounded-xl border border-white/5">
                    <p class="text-xs text-white/70 line-clamp-2 leading-relaxed">
                        {{ $author->bio ?: 'No biography available for this author yet.' }}
                    </p>
                </div>
                
                <div class="divider my-2"></div>
                
                <div class="flex items-center justify-between bg-base-100/30 p-3 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2">
                        <div class="bg-primary/20 p-2 rounded-lg">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-white">{{ $author->books->count() }}</span>
                            <span class="text-[10px] uppercase tracking-wider text-white/70 block font-bold leading-tight">Published Books</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-actions justify-end gap-1 mt-2">
                    <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-ghost hover:bg-primary/20 hover:text-primary transition-all duration-300 rounded-lg group" title="View Profile">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <button @click="openEditModal({
                        id: '{{ $author->id }}',
                        name: '{{ addslashes($author->name) }}',
                        bio: '{{ addslashes($author->bio) }}'
                    })" class="btn btn-sm btn-ghost hover:bg-warning/20 hover:text-warning transition-all duration-300 rounded-lg group" title="Edit Author">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('authors.destroy', $author) }}" class="inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-ghost hover:bg-error/20 hover:text-error transition-all duration-300 rounded-lg group confirm-delete" title="Delete Author">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="bg-base-200 rounded-2xl shadow-sm border border-base-300 p-12 text-center">
                <div class="space-y-2">
                    <div class="text-5xl">✍️</div>
                    <p class="font-bold text-2xl text-base-content">No authors found</p>
                    @if(!request('search'))
                        <button @click="showCreateModal = true" class="link link-primary font-semibold">Create your first author</button> to get started
                    @endif
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-8 flex justify-center">
    {{ $authors->links() }}
</div>
