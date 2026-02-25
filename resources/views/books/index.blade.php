<x-app-layout>
    <div class="space-y-0" x-data="bookCatalog()">

        @guest
        {{-- ===== STUDENT HERO SECTION ===== --}}
        <div class="relative overflow-hidden rounded-3xl mb-8" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 75%, #533483 100%);">
            {{-- Decorative blobs --}}
            <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #e94560, transparent 70%);"></div>
            <div class="absolute -bottom-24 -left-16 w-80 h-80 rounded-full opacity-10" style="background: radial-gradient(circle, #533483, transparent 70%);"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-5 pointer-events-none" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>

            <div class="relative z-10 p-10 md:p-16">
                <div class="flex flex-col md:flex-row items-center gap-10">
                    {{-- Left: Text content --}}
                    <div class="flex-1 text-center md:text-left space-y-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest" style="background: rgba(255,255,255,0.1); color: #a78bfa; backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.1);">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block"></span>
                            Library Now Open
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tight">
                            Your Next Great<br>
                            <span style="background: linear-gradient(90deg, #a78bfa, #60a5fa, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Adventure Awaits</span>
                        </h1>
                        <p class="text-lg text-white/60 max-w-lg leading-relaxed">
                            Discover thousands of books, expand your knowledge, and fuel your curiosity. The library is your gateway to unlimited worlds.
                        </p>

                        {{-- Search Bar in Hero --}}
                        <div class="relative max-w-lg">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <template x-if="!isLoading">
                                    <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </template>
                                <template x-if="isLoading">
                                    <span class="loading loading-spinner loading-xs text-purple-400"></span>
                                </template>
                            </div>
                            <input
                                type="text"
                                x-model="search"
                                @input.debounce.500ms="performSearch()"
                                placeholder="Search by title or author..."
                                class="w-full pl-14 pr-12 py-4 rounded-2xl text-white placeholder-white/40 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 transition-all"
                                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);"
                            >
                            <button
                                x-show="search.length > 0"
                                @click="search = ''; performSearch()"
                                class="absolute inset-y-0 right-0 pr-5 flex items-center text-white/30 hover:text-white transition-colors"
                                style="display: none;"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Right: Stats cards --}}
                    <div class="flex md:flex-col gap-4 shrink-0">
                        <div class="flex flex-col items-center px-8 py-5 rounded-2xl text-center" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                            <div class="text-3xl font-black text-white">{{ \App\Models\Book::count() }}</div>
                            <div class="text-xs text-white/50 font-semibold uppercase tracking-widest mt-1">Books</div>
                        </div>
                        <div class="flex flex-col items-center px-8 py-5 rounded-2xl text-center" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                            <div class="text-3xl font-black" style="color: #34d399;">{{ \App\Models\Book::where('available_quantity', '>', 0)->count() }}</div>
                            <div class="text-xs text-white/50 font-semibold uppercase tracking-widest mt-1">Available</div>
                        </div>
                        <div class="flex flex-col items-center px-8 py-5 rounded-2xl text-center" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                            <div class="text-3xl font-black" style="color: #a78bfa;">{{ \App\Models\Author::count() }}</div>
                            <div class="text-xs text-white/50 font-semibold uppercase tracking-widest mt-1">Authors</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endguest

        @auth
        {{-- ===== ADMIN COMPACT HEADER ===== --}}
        <div class="bg-base-200 text-base-content rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-base-300 mb-6">
            <div>
                <h1 class="text-4xl font-bold">Books Catalog</h1>
                <p class="text-lg opacity-60 mt-2 font-medium">Explore and manage your library collection</p>
            </div>
            <div class="flex-grow max-w-md w-full">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <template x-if="!isLoading">
                            <svg class="h-5 w-5 text-base-content/30 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </template>
                        <template x-if="isLoading">
                            <span class="loading loading-spinner loading-xs text-primary"></span>
                        </template>
                    </div>
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500ms="performSearch()"
                        placeholder="Search by title or author..."
                        class="input input-bordered w-full pl-12 bg-base-100/50 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-2xl h-14 transition-all"
                    >
                    <button
                        x-show="search.length > 0"
                        @click="search = ''; performSearch()"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-base-content/30 hover:text-error transition-colors"
                        style="display: none;"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <button @click="showCreateModal = true" class="btn btn-primary btn-lg rounded-xl shadow-md transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Book
            </button>
        </div>
        @endauth

        {{-- ===== BOOKS CONTENT ===== --}}
        <div id="books-table-content">

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
                <div class="group bg-base-100 rounded-3xl overflow-hidden border border-base-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    {{-- Book Cover --}}
                    <div class="relative aspect-[3/4] overflow-hidden bg-base-200">
                        <img
                            src="{{ $book->cover_image ?: asset('build/images/default-book-cover.png') }}"
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
                <button @click="search = ''; performSearch()" class="btn btn-primary btn-sm rounded-xl mt-2">Clear Search</button>
            </div>
            @endif
            @endguest

            @auth
            {{-- ===== ADMIN TABLE VIEW ===== --}}
            <div class="bg-base-100 rounded-2xl shadow-sm border border-base-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-base-200 text-base-content border-b border-base-300">
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
                                <tr class="hover:bg-primary/10 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-16 rounded-lg bg-base-300 flex-shrink-0 overflow-hidden shadow-sm border border-base-content/5">
                                                <img src="{{ $book->cover_image ?: asset('build/images/default-book-cover.png') }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <div class="font-bold text-base text-base-content">{{ $book->title }}</div>
                                                <div class="text-sm opacity-70 italic">{{ $book->publisher ?? 'Independent' }}</div>
                                                <div class="text-xs opacity-50">Published: {{ $book->published_year ?? 'Unknown' }}</div>
                                            </div>
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
                                            <button @click="openDetailsModal({{ $bookJson }})" class="btn btn-sm btn-ghost hover:bg-info/20 hover:text-info transition-all duration-300 rounded-lg group" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button @click="openEditModal({{ $bookJson }})" class="btn btn-sm btn-ghost hover:bg-warning/20 hover:text-warning transition-all duration-300 rounded-lg group" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" @click="confirmDelete('{{ $book->id }}')" class="btn btn-sm btn-ghost hover:bg-error/20 hover:text-error transition-all duration-300 rounded-lg group" title="Delete">
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

        </div>{{-- end #books-table-content --}}


        <!-- Create Modal -->
        <div class="modal" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-2xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Add New Book</h3>
                    <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form @submit.prevent="submitCreate()" class="space-y-6" x-data="{ loading: false }">
                    @csrf
                    
                    <!-- Full Width Title -->
                    <div class="form-control">
                        <label class="label focus-within:text-primary transition-colors"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Book Title</span></label>
                        <input type="text" name="title" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl h-14 text-lg font-bold" required placeholder="Enter book title">
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Left Side: Image Upload & Preview -->
                        <div class="w-full lg:w-2/5 group">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60 font-bold">Book Cover Image</span></label>
                            <div class="relative w-full aspect-[3/4.5] rounded-3xl border-2 border-dashed border-base-300 flex flex-col items-center justify-center overflow-hidden hover:border-primary hover:bg-primary/5 transition-all duration-300 bg-base-200/50 shadow-inner group">
                                <template x-if="!createImagePreview">
                                    <div class="text-center p-6 space-y-3">
                                        <div class="w-16 h-16 bg-base-300 rounded-2xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-500">
                                            <svg class="w-8 h-8 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs opacity-40 font-bold uppercase tracking-wider">Tap to Select Cover</p>
                                    </div>
                                </template>
                                <template x-if="createImagePreview">
                                    <div class="relative w-full h-full group/preview">
                                        <img :src="createImagePreview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click="createImagePreview = ''; $refs.createFileInput.value = ''" class="btn btn-error btn-sm rounded-xl font-bold">Remove Image</button>
                                        </div>
                                    </div>
                                </template>
                                <input type="file" x-ref="createFileInput" @change="handleImageUpload($event, 'create')" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                            <input type="hidden" name="cover_image" :value="createImagePreview">
                        </div>

                        <!-- Right Side: Grid of Info -->
                        <div class="flex-grow space-y-6">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Publisher</span></label>
                                <input type="text" name="publisher" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" placeholder="e.g. Penguin Books">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Published Year</span></label>
                                    <input type="number" name="published_year" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" placeholder="{{ date('Y') }}">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Total Quantity</span></label>
                                    <input type="number" name="total_quantity" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl font-bold" required min="1" placeholder="1">
                                </div>
                            </div>

                            <div class="form-control" x-data="{ open: false, selectedAuthors: [] }">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Authors Information</span></label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" class="input input-bordered w-full flex items-center justify-between bg-base-200 border-base-300 rounded-xl text-sm px-4">
                                        <span x-text="selectedAuthors.length ? selectedAuthors.length + ' authors selected' : 'Assign authors...'" class="font-medium"></span>
                                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute z-50 w-full mt-2 bg-base-100 border border-base-200 rounded-2xl shadow-2xl max-h-60 overflow-y-auto p-2 scrollbar-thin">
                                        @foreach (\App\Models\Author::all() as $author)
                                            <label class="flex items-center gap-3 p-3 hover:bg-primary/5 rounded-xl cursor-pointer transition-colors group">
                                                <input type="checkbox" name="authors[]" value="{{ $author->id }}" x-model="selectedAuthors" class="checkbox checkbox-primary checkbox-sm rounded-md">
                                                <span class="text-sm font-semibold text-base-content/80 group-hover:text-primary transition-colors">{{ $author->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description - Widened -->
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Book Description & Summary</span></label>
                        <textarea name="description" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-2xl h-40 leading-relaxed text-sm py-4" placeholder="Brief book description..."></textarea>
                    </div>

                    <div class="modal-action border-t border-base-200 pt-6 mt-8">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl px-8" :disabled="loading">Discard</button>
                        <button type="submit" class="btn btn-primary rounded-xl px-12 shadow-lg shadow-primary/20" :class="{ 'loading': loading }" :disabled="loading">
                            <span x-show="!loading">Create Book Entry</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-xs"></span>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal" :class="{ 'modal-open': showEditModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-2xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Edit Book</h3>
                    <button @click="showEditModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form @submit.prevent="submitEdit()" class="space-y-6" x-data="{ loading: false }">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Full Width Title -->
                    <div class="form-control">
                        <label class="label focus-within:text-primary transition-colors"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Book Title</span></label>
                        <input type="text" name="title" x-model="editData.title" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl h-14 text-lg font-bold" required>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Left Side: Image Upload & Preview -->
                        <div class="w-full lg:w-2/5 group">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60 font-bold">Book Cover Image</span></label>
                            <div class="relative w-full aspect-[3/4.5] rounded-3xl border-2 border-dashed border-base-300 flex flex-col items-center justify-center overflow-hidden hover:border-primary hover:bg-primary/5 transition-all duration-300 bg-base-200/50 shadow-inner group">
                                <template x-if="!editData.cover_image">
                                    <div class="text-center p-6 space-y-3">
                                        <div class="w-16 h-16 bg-base-300 rounded-2xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-500">
                                            <svg class="w-8 h-8 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs opacity-40 font-bold uppercase tracking-wider">Tap to Select Cover</p>
                                    </div>
                                </template>
                                <template x-if="editData.cover_image">
                                    <div class="relative w-full h-full group/preview">
                                        <img :src="editData.cover_image" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click="editData.cover_image = ''; $refs.editFileInput.value = ''" class="btn btn-error btn-sm rounded-xl font-bold">Remove Image</button>
                                        </div>
                                    </div>
                                </template>
                                <input type="file" x-ref="editFileInput" @change="handleImageUpload($event, 'edit')" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                            <input type="hidden" name="cover_image" :value="editData.cover_image">
                        </div>

                        <!-- Right Side: Grid of Info -->
                        <div class="flex-grow space-y-6">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Publisher</span></label>
                                <input type="text" name="publisher" x-model="editData.publisher" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Published Year</span></label>
                                    <input type="number" name="published_year" x-model="editData.published_year" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Total Quantity</span></label>
                                    <input type="number" name="total_quantity" x-model="editData.total_quantity" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl font-bold" required min="1">
                                </div>
                            </div>

                            <div class="form-control" x-data="{ open: false }">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Authors Information</span></label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" class="input input-bordered w-full flex items-center justify-between bg-base-200 border-base-300 rounded-xl text-sm px-4">
                                        <span x-text="editData.authors.length ? editData.authors.length + ' authors selected' : 'Assign authors...'" class="font-medium"></span>
                                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute z-50 w-full mt-2 bg-base-100 border border-base-200 rounded-2xl shadow-2xl max-h-60 overflow-y-auto p-2 scrollbar-thin">
                                        @foreach (\App\Models\Author::all() as $author)
                                            <label class="flex items-center gap-3 p-3 hover:bg-primary/5 rounded-xl cursor-pointer transition-colors group">
                                                <input type="checkbox" name="authors[]" value="{{ $author->id }}" x-model="editData.authors" class="checkbox checkbox-primary checkbox-sm rounded-md">
                                                <span class="text-sm font-semibold text-base-content/80 group-hover:text-primary transition-colors">{{ $author->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description - Widened -->
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Book Description & Summary</span></label>
                        <textarea name="description" x-model="editData.description" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-2xl h-40 leading-relaxed text-sm py-4"></textarea>
                    </div>

                    <div class="modal-action border-t border-base-200 pt-6 mt-8">
                        <button type="button" @click="showEditModal = false" class="btn btn-ghost rounded-xl px-8" :disabled="loading">Discard</button>
                        <button type="submit" class="btn btn-warning rounded-xl px-12 shadow-lg shadow-warning/20 text-warning-content font-bold" :class="{ 'loading': loading }" :disabled="loading">
                            <span x-show="!loading">Update Book Data</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-xs"></span>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Details Modal -->
        <div class="modal modal-bottom sm:modal-middle" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box w-full max-w-2xl rounded-t-[2.5rem] sm:rounded-[2.5rem] p-0 border border-white/10 shadow-3xl overflow-hidden bg-base-100 flex flex-col max-h-[90vh]">
                <div class="bg-info p-6 sm:p-8 flex justify-between items-center text-info-content shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold tracking-tight">Book Details</h3>
                            <p class="text-[10px] sm:text-sm opacity-80 font-medium mt-0.5 sm:mt-1">Full catalog information</p>
                        </div>
                    </div>
                    <button @click="showDetailsModal = false" class="btn btn-sm btn-circle btn-ghost text-white">✕</button>
                </div>

                <div class="p-6 sm:p-8 space-y-6 sm:space-y-8 overflow-y-auto custom-scrollbar flex-grow">
                    <div class="flex flex-col sm:flex-row gap-8">
                        <!-- Book Cover in Details -->
                        <div class="w-full sm:w-48 shrink-0">
                            <div class="aspect-[3/4] rounded-2xl bg-base-300 shadow-xl overflow-hidden border border-white/10 ring-1 ring-base-content/5">
                                <img :src="showData.cover_image || '{{ asset('build/images/default-book-cover.png') }}'" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow space-y-4">
                            <div class="space-y-2">
                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-40">Title & Publisher</div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-base-content" x-text="showData.title"></h2>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm opacity-60 italic">
                                    <span x-text="showData.publisher || 'Independent'"></span>
                                    <span class="opacity-20 hidden sm:inline">•</span>
                                    <span x-text="'Published ' + (showData.published_year || 'Unknown')"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-base-200/50 p-4 rounded-3xl border border-base-300">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-40 mb-1">Availability</div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl font-bold text-primary" x-text="showData.available_quantity"></span>
                                        <span class="opacity-40">/</span>
                                        <span class="text-lg opacity-60" x-text="showData.total_quantity + ' total'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-40">Contributing Authors</div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="author in showData.authors" :key="author.id">
                                <div class="badge badge-primary badge-outline sm:badge-lg py-3 sm:py-4 px-3 sm:px-4 rounded-xl" x-text="author.name"></div>
                            </template>
                            <template x-if="!showData.authors || showData.authors.length === 0">
                                <span class="text-sm opacity-50 italic">No authors listed</span>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-40">Description</div>
                        <div class="bg-base-200/30 p-4 sm:p-6 rounded-3xl text-sm leading-relaxed opacity-80" x-text="showData.description || 'No description available for this book.'"></div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 bg-base-200/50 border-t border-base-300 shrink-0">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="showDetailsModal = false" class="btn btn-ghost rounded-xl px-10 order-2 sm:order-1">Close</button>
                        @guest
                            <template x-if="showData.available_quantity > 0">
                                <button @click="showDetailsModal = false; borrowData = { id: showData.id, title: showData.title, available: showData.available_quantity }; showBorrowModal = true" class="btn btn-success text-success-content font-bold rounded-xl px-10 shadow-lg shadow-success/20 order-1 sm:order-2 flex-grow">Borrow This Book</button>
                            </template>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrow Modal (For Students/Guests) -->
        <div class="modal modal-bottom sm:modal-middle" :class="{ 'modal-open': showBorrowModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box w-full max-w-xl rounded-t-[2.5rem] sm:rounded-[2.5rem] p-0 border border-white/10 shadow-3xl overflow-hidden bg-base-100 flex flex-col max-h-[90vh]">
                <div class="bg-success p-6 sm:p-8 flex justify-between items-center text-success-content shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold tracking-tight">Borrow Book</h3>
                            <p class="text-[10px] sm:text-sm opacity-80 font-medium mt-0.5 sm:mt-1">Limited to available library stock</p>
                        </div>
                    </div>
                    <button @click="showBorrowModal = false" class="btn btn-sm btn-circle btn-ghost text-white">✕</button>
                </div>

                <form action="{{ route('borrow-transactions.store') }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                    @csrf
                    <input type="hidden" name="book_id" :value="borrowData.id">
                    <input type="hidden" name="book_title" :value="borrowData.title">
                    <input type="hidden" name="available_count" :value="borrowData.available">
                    <input type="hidden" name="studentSearch" :value="studentSearch">
                    <input type="hidden" name="student_id" :value="selectedStudent ? selectedStudent.id : '{{ old('student_id') }}'">

                    <div class="p-6 sm:p-8 space-y-6 overflow-y-auto custom-scrollbar flex-grow">
                        @if($errors->any())
                            <div class="alert alert-error rounded-2xl text-xs py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Please check the errors below.</span>
                            </div>
                        @endif

                        <div class="bg-base-200/50 p-4 sm:p-6 rounded-3xl border border-base-300">
                            <div class="text-[10px] font-bold uppercase tracking-widest opacity-40 mb-1">Selected Book</div>
                            <div class="text-lg sm:text-xl font-bold text-primary" x-text="borrowData.title"></div>
                            <div class="text-[10px] sm:text-xs mt-1 opacity-60"><span x-text="borrowData.available"></span> copies available for pickup</div>
                        </div>

                        <div class="form-control relative">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Student Profile *</span></label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="studentSearch" 
                                    @focus="showStudentDropdown = true"
                                    @click.away="showStudentDropdown = false"
                                    placeholder="Type your name to search..." 
                                    class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl w-full pr-10 @error('student_id') border-error @enderror"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none opacity-30">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div 
                                    x-show="showStudentDropdown" 
                                    class="absolute z-50 w-full mt-2 bg-base-100 border border-base-300 rounded-2xl shadow-xl max-h-48 overflow-y-auto custom-scrollbar"
                                    style="display: none;"
                                >
                                    <template x-for="student in filteredStudents()" :key="student.id">
                                        <button 
                                            type="button" 
                                            @click="selectStudent(student)"
                                            class="w-full text-left px-4 py-3 hover:bg-primary/10 transition-colors text-sm border-b border-base-200 last:border-none"
                                        >
                                            <span class="font-medium" x-text="student.name"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredStudents().length === 0" class="px-4 py-3 text-sm opacity-50 italic">No matching students found</div>
                                </div>
                            </div>
                            @error('student_id') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Personal PIN *</span></label>
                            <input type="password" name="pin" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('pin') border-error @enderror" required placeholder="••••" minlength="4" maxlength="6">
                            <label class="label"><span class="label-text-alt opacity-50 text-[10px] sm:text-xs text-info">Enter your 4-6 digit security PIN</span></label>
                            @error('pin') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Quantity *</span></label>
                                <input type="number" name="quantity_borrowed" value="{{ old('quantity_borrowed', 1) }}" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl w-full @error('quantity_borrowed') border-error @enderror" required min="1" :max="borrowData.available">
                                @error('quantity_borrowed') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Return Date *</span></label>
                                <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl w-full @error('due_date') border-error @enderror" required min="{{ now()->addDays(1)->format('Y-m-d') }}">
                                @error('due_date') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info rounded-2xl text-[10px] sm:text-xs bg-primary/5 border-primary/10 text-primary-content/80">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Policy: ₱10.00 fine per day per book if returned after the due date.</span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 bg-base-200/50 border-t border-base-300 shrink-0">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" @click="showBorrowModal = false" class="btn btn-ghost rounded-xl order-2 sm:order-1">Cancel</button>
                            <button type="submit" class="btn btn-success text-success-content font-bold rounded-xl px-10 shadow-lg shadow-success/20 order-1 sm:order-2 flex-grow">Record Borrow</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('bookCatalog', () => ({
                    showCreateModal: false,
                    showEditModal: false,
                    editData: { id: '', title: '', publisher: '', published_year: '', total_quantity: '', description: '', authors: [], cover_image: '' },
                    showBorrowModal: {{ $errors->any() && old('book_id') ? 'true' : 'false' }},
                    borrowData: { 
                        id: '{{ old('book_id') }}', 
                        title: '{{ old('book_title', 'Previously Selected Book') }}', 
                        available: {{ old('available_count', 0) }} 
                    },
                    showDetailsModal: false,
                    showData: { id: '', title: '', publisher: '', published_year: '', total_quantity: '', available_quantity: '', description: '', authors: [], cover_image: '' },
                    search: '{{ $search ?? '' }}',
                    isLoading: false,

                    handleImageUpload(event, type) {
                        const file = event.target.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (type === 'create') {
                                // For create modal, we'll use a hidden input or handle it differently if needed
                                // but the state belongs to the form. We'll set a preview variable.
                                this.createImagePreview = e.target.result;
                            } else {
                                this.editData.cover_image = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    },
                    createImagePreview: '',
                    students: @json(\App\Models\Student::orderBy('name')->get(['id', 'name'])),
                    studentSearch: '{{ old('studentSearch') }}',
                    selectedStudent: @json(\App\Models\Student::find(old('student_id'))),
                    showStudentDropdown: false,

                    filteredStudents() {
                        if (!this.studentSearch) return this.students;
                        return this.students.filter(s => s.name.toLowerCase().includes(this.studentSearch.toLowerCase()));
                    },

                    selectStudent(student) {
                        this.selectedStudent = student;
                        this.studentSearch = student.name;
                        this.showStudentDropdown = false;
                    },

                    openEditModal(book) {
                        this.editData = { 
                            id: book.id,
                            title: book.title,
                            publisher: book.publisher || '',
                            published_year: book.published_year || '',
                            total_quantity: book.total_quantity,
                            description: book.description || '',
                            cover_image: book.cover_image || '{{ asset('build/images/default-book-cover.png') }}',
                            authors: book.authors.map(a => a.id.toString())
                        };
                        this.showEditModal = true;
                    },

                    openDetailsModal(book) {
                        this.showData = { ...book };
                        this.showDetailsModal = true;
                    },

                    async submitCreate() {
                        this.loading = true;
                        const formData = new FormData(this.$event.target);
                        
                        try {
                            const response = await fetch('{{ route('books.store') }}', {
                                method: 'POST',
                                body: formData,
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const result = await response.json();
                            
                            if (result.success) {
                                this.showCreateModal = false;
                                this.$event.target.reset();
                                this.createImagePreview = '';
                                await this.syncTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: result.message,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        } catch (error) {
                            console.error('Submission failed:', error);
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                        } finally {
                            this.loading = false;
                        }
                    },

                    async submitEdit() {
                        this.loading = true;
                        const formData = new FormData(this.$event.target);
                        
                        try {
                            const response = await fetch(`{{ url('books') }}/${this.editData.id}`, {
                                method: 'POST',
                                body: formData,
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const result = await response.json();
                            
                            if (result.success) {
                                this.showEditModal = false;
                                await this.syncTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: result.message,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        } catch (error) {
                            console.error('Update failed:', error);
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Update failed. Please try again.' });
                        } finally {
                            this.loading = false;
                        }
                    },

                    confirmDelete(id) {
                        Swal.fire({
                            title: 'Permanently remove book?',
                            text: "This will delete the book and its entire history!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ff5861',
                            cancelButtonColor: '#355872',
                            confirmButtonText: 'Yes, delete it!',
                            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                try {
                                    const response = await fetch(`{{ url('books') }}/${id}`, {
                                        method: 'DELETE',
                                        headers: { 
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    });
                                    const res = await response.json();
                                    if (res.success) {
                                        await this.syncTable();
                                        Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                                    }
                                } catch (error) {
                                    console.error('Delete failed:', error);
                                }
                            }
                        });
                    },

                    async syncTable() {
                        this.isLoading = true;
                        const url = new URL(window.location.href);
                        if (this.search.length > 0) {
                            url.searchParams.set('search', this.search);
                        } else {
                            url.searchParams.delete('search');
                        }

                        try {
                            const response = await fetch(url.toString(), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const html = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            const newTable = doc.getElementById('books-table-content');
                            const currentTable = document.getElementById('books-table-content');
                            if (newTable && currentTable) {
                                currentTable.innerHTML = newTable.innerHTML;
                            }

                            window.history.pushState({}, '', url.toString());
                        } catch (error) {
                            console.error('Sync failed:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async performSearch() {
                        await this.syncTable();
                    }
                }));
            });

        </script>

        </div>
    </div>
</x-app-layout>
