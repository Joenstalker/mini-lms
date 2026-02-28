@guest
{{-- ===== STUDENT CARD GRID VIEW ===== --}}
@if(isset($search) && $search)
<p class="text-sm text-base-content/50 mb-5 px-1">Showing results for <span class="font-bold text-primary">"{{ $search }}"</span></p>
@endif

@if($books->count() > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach ($books as $book)
    @php
        $bookJson = json_encode([
            'id' => $book->id,
            'title' => $book->title,
            'publisher' => $book->publisher,
            'published_year' => $book->published_year,
            'total_quantity' => $book->total_quantity,
            'available_quantity' => $book->available_quantity,
            'description' => $book->description,
            'cover_image' => $book->cover_image,
            'authors' => $book->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
        ]);
    @endphp
    <div class="group glass-card rounded-3xl overflow-hidden border border-white/10 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 flex flex-col">
        {{-- Book Cover --}}
        <div class="relative aspect-[3/4] overflow-hidden bg-base-200">
            <img
                src="{{ $book->cover_image ? (Str::startsWith($book->cover_image, 'http') ? $book->cover_image : '/images/' . $book->cover_image) : '/images/default-book-cover.png' }}"
                alt="{{ $book->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            >
            {{-- Availability badge --}}
            <div class="absolute top-3 right-3">
                @if($book->available_quantity > 0)
                    <span class="badge badge-success font-bold shadow-lg text-xs px-3 py-2">
                        {{ $book->available_quantity }} left
                    </span>
                @else
                    <span class="badge badge-error font-bold shadow-lg text-xs px-3 py-2">Unavailable</span>
                @endif
            </div>
            {{-- Hover overlay with quick actions --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 gap-2">
                <button @click="openDetailsModal({{ $bookJson }})" class="btn btn-sm w-full rounded-xl bg-white/20 backdrop-blur-sm border-white/30 text-white hover:bg-white/30 font-bold text-xs">
                    View Details
                </button>
                @if($book->available_quantity > 0)
                <button @click="borrowData = { id: '{{ $book->id }}', title: {{ json_encode($book->title) }}, available: {{ $book->available_quantity }} }; showBorrowModal = true"
                    class="btn btn-sm w-full rounded-xl font-bold text-xs"
                    style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                    🔖 Borrow This Book
                </button>
                @endif
            </div>
        </div>

        {{-- Book Info --}}
        <div class="p-4 flex flex-col gap-2 flex-grow">
            <h3 class="font-bold text-base text-base-content leading-snug line-clamp-2">{{ $book->title }}</h3>
            <div class="text-xs text-base-content/50 italic">{{ $book->publisher ?? 'Independent' }}</div>
            <div class="flex flex-wrap gap-1 mt-auto pt-2">
                @foreach($book->authors->take(2) as $author)
                    <a href="{{ route('authors.show', $author) }}" class="badge badge-ghost badge-sm hover:badge-primary transition-colors text-[10px]">{{ $author->name }}</a>
                @endforeach
                @if($book->authors->count() > 2)
                    <span class="badge badge-ghost badge-sm text-[10px]">+{{ $book->authors->count() - 2 }} more</span>
                @endif
            </div>
        </div>

        {{-- Borrow CTA Button --}}
        <div class="px-4 pb-4">
            @if($book->available_quantity > 0)
                <button @click="borrowData = { id: '{{ $book->id }}', title: {{ json_encode($book->title) }}, available: {{ $book->available_quantity }} }; showBorrowModal = true"
                    class="btn btn-success btn-sm w-full rounded-xl font-bold text-success-content shadow-md hover:shadow-success/30">
                    Borrow Now
                </button>
            @else
                <button class="btn btn-sm w-full rounded-xl font-bold btn-disabled opacity-50" disabled>
                    Out of Stock
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-24 space-y-4">
    <div class="text-6xl">🔍</div>
    <p class="text-2xl font-bold text-base-content/70">No books found</p>
    <p class="text-base-content/40 text-sm">Try searching with a different keyword</p>
</div>
@endif
@endguest

@auth
{{-- ===== ADMIN TABLE VIEW ===== --}}
<div class="glass-card rounded-2xl shadow-xl border border-white/10 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table w-full text-white border-separate border-spacing-y-2">
            <thead class="bg-white/5 text-white border-b border-white/10">
                <tr>
                    <th class="text-sm font-bold rounded-tl-lg">Title & Publisher</th>
                    <th class="text-sm font-bold">Authors</th>
                    <th class="text-center text-sm font-bold">Available</th>
                    <th class="text-center text-sm font-bold">Total</th>
                    <th class="text-sm font-bold rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr class="hover:bg-white/10 transition-colors group glass-card">
                        <td>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 rounded-lg bg-base-300 flex-shrink-0 overflow-hidden shadow-sm border border-base-content/5">
                                    <img src="{{ $book->cover_image ? (Str::startsWith($book->cover_image, 'http') ? $book->cover_image : '/images/' . $book->cover_image) : '/images/default-book-cover.png' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-base text-white">{{ $book->title }}</div>
                                    <div class="text-sm text-white/70 italic">{{ $book->publisher ?? 'Independent' }}</div>
                                    <div class="text-xs text-white/50">Published: {{ $book->published_year ?? 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($book->authors as $author)
                                    <a href="{{ route('authors.show', $author) }}" class="badge badge-primary badge-outline text-white hover:bg-primary transition-all duration-300 border-white/20">{{ $author->name }}</a>
                                @empty
                                    <span class="text-xs text-white/50 italic">No authors</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-center">
                            @if ($book->available_quantity > 0)
                                <span class="badge badge-success badge-lg font-bold">{{ $book->available_quantity }}</span>
                            @else
                                <span class="badge badge-error badge-lg font-bold">Out</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-outline badge-lg text-white border-white/20">{{ $book->total_quantity }}</span>
                        </td>
                        <td>
                            <div class="flex gap-1 shrink-0">
                                @php
                                    $bookJson = json_encode([
                                        'id' => $book->id,
                                        'title' => $book->title,
                                        'publisher' => $book->publisher,
                                        'published_year' => $book->published_year,
                                        'total_quantity' => $book->total_quantity,
                                        'available_quantity' => $book->available_quantity,
                                        'description' => $book->description,
                                        'cover_image' => $book->cover_image,
                                        'authors' => $book->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
                                    ]);
                                @endphp
                                <button @click="openDetailsModal({{ $bookJson }})" class="btn btn-sm btn-ghost hover:bg-info/20 text-white/70 hover:text-info transition-all duration-300 rounded-lg group" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button @click="openEditModal({{ $bookJson }})" class="btn btn-sm btn-ghost hover:bg-warning/20 text-white/70 hover:text-warning transition-all duration-300 rounded-lg group" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" @click="confirmDelete('{{ $book->id }}')" class="btn btn-sm btn-ghost hover:bg-error/20 text-white/70 hover:text-error transition-all duration-300 rounded-lg group" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-base-content/60">
                            <div class="space-y-2">
                                <div class="text-4xl">📭</div>
                                <p class="font-semibold text-lg">No books found</p>
                                <button @click="showCreateModal = true" class="link link-primary font-semibold">Add your first book</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endauth

<div class="mt-8 flex justify-center">
    {{ $books->links() }}
</div>
