<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row gap-6 items-center bg-white/5 p-6 rounded-3xl border border-white/10 backdrop-blur-sm">
        <div class="avatar shadow-2xl border-4 border-white/10 rounded-2xl overflow-hidden shrink-0">
            <div class="w-24 h-24 md:w-32 md:h-32 bg-primary/20 flex items-center justify-center">
                @if($author->profile_image)
                    <img src="{{ $author->profile_image }}" class="w-full h-full object-cover">
                @else
                    <span class="text-white font-black text-4xl">{{ substr($author->name, 0, 1) }}</span>
                @endif
            </div>
        </div>
        <div class="text-center md:text-left">
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">{{ $author->name }}</h2>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                <span class="px-3 py-1 bg-primary/10 border border-primary/20 rounded-full text-[10px] font-black uppercase tracking-widest text-primary">Professional Author</span>
                <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-widest text-white/40">ID: #{{ str_pad($author->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Biography --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-primary ring-4 ring-primary/20"></div>
                <h3 class="text-[10px] uppercase tracking-[0.2em] font-black text-white/30">Biography</h3>
            </div>
            <div class="bg-white/5 p-6 rounded-3xl border border-white/10 min-h-[120px]">
                @if ($author->bio)
                    <p class="text-white/80 leading-relaxed">{{ $author->bio }}</p>
                @else
                    <p class="text-white/30 italic">No biography available for this author.</p>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-blue-400 ring-4 ring-blue-400/20"></div>
                <h3 class="text-[10px] uppercase tracking-[0.2em] font-black text-white/30">Quick Stats</h3>
            </div>
            <div class="bg-gradient-to-br from-primary/10 to-transparent p-6 rounded-3xl border border-white/10 text-center">
                <div class="text-5xl font-black text-primary">{{ $author->books->count() }}</div>
                <div class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 mt-2">Books Contributed</div>
            </div>
        </div>
    </div>

    {{-- Catalog --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <div class="h-1.5 w-1.5 rounded-full bg-green-400 ring-4 ring-green-400/20"></div>
            <h3 class="text-[10px] uppercase tracking-[0.2em] font-black text-white/30">Published Catalog</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse ($author->books as $book)
                <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-all group">
                    <div class="w-10 h-14 bg-white/5 rounded-lg overflow-hidden shrink-0 shadow-lg border border-white/10 group-hover:border-primary/50 transition-colors">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-primary/20 text-primary uppercase font-bold text-[8px]">No Cover</div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-white text-sm truncate">{{ $book->title }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-black uppercase text-white/30 tracking-widest">Available:</span>
                            <span class="text-[9px] font-black {{ $book->available_quantity > 0 ? 'text-green-400' : 'text-error' }}">
                                {{ $book->available_quantity }}/{{ $book->total_quantity }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center bg-white/5 rounded-2xl border border-dashed border-white/10 text-white/20 text-[10px] uppercase font-bold tracking-widest">
                    No books assigned to this author yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
