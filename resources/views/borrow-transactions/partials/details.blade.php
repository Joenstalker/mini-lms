<!-- Data for Live Countdown -->
<input type="hidden" id="details-due-date" value="{{ $borrowTransaction->due_date->toIso8601String() }}">
<input type="hidden" id="details-status" value="{{ $borrowTransaction->status }}">
<div id="details-active" class="{{ $borrowTransaction->status !== 'returned' ? '' : 'hidden' }}"></div>

<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Student Information -->
        <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-8 right-8 text-white/10 group-hover:text-white/20 transition-all duration-500">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            
            <h4 class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-6">Student Information</h4>
            <div class="space-y-4">
                <div>
                    <span class="text-[11px] font-bold text-white/40 block mb-1">Full Name</span>
                    <span class="text-2xl font-black text-primary tracking-tight">{{ $borrowTransaction->student->name }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-white/40 block mb-1">Email Address</span>
                    <span class="text-sm font-bold text-white/50 tracking-tight">{{ $borrowTransaction->student->email }}</span>
                </div>
            </div>
        </div>

        <!-- Book Information -->
        <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-8 right-8 text-white/10 group-hover:text-white/20 transition-all duration-500">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.434.29-3.48.804v10.009a8.808 8.808 0 012.89-.482c1.545 0 3.022.385 4.319 1.054V4.804zM10 14.817a8.59 8.59 0 011.604-.467c1.373-.323 2.801-.482 4.272-.482 1.546 0 3.022.385 4.319 1.054V4.804A7.968 7.968 0 0017.5 4c-1.255 0-2.434.29-3.48.804v10.009c.467-.226.96-.402 1.482-.526a8.59 8.59 0 00-1.482-.526V4.804z"></path>
                </svg>
            </div>
            
            <h4 class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-6">Book Information</h4>
            <div class="space-y-4">
                <div>
                    <span class="text-[11px] font-bold text-white/40 block mb-1">Book Title</span>
                    <span class="text-xl font-black text-[#ff2d60] tracking-tight leading-tight block truncate">{{ $borrowTransaction->book->title }}</span>
                </div>
                <div class="flex gap-10">
                    <div>
                        <span class="text-[10px] font-bold text-white/30 block mb-1">Borrowed</span>
                        <span class="font-black text-sm text-white/80 uppercase">{{ $borrowTransaction->borrow_date->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-white/30 block mb-1">Due Date</span>
                        <span class="font-black text-sm uppercase {{ $borrowTransaction->is_overdue ? 'text-error' : 'text-white/80' }}">
                            {{ $borrowTransaction->due_date->format('M d, Y') }}
                            @if($borrowTransaction->is_overdue)
                                <span class="ml-1 text-[8px] bg-error text-white px-2 py-0.5 rounded-full">OVERDUE</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 flex flex-col items-center justify-center text-center shadow-inner">
            <span class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-2">Quantity</span>
            <span class="text-4xl font-black text-white">{{ $borrowTransaction->quantity_borrowed }}</span>
        </div>
        <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 flex flex-col items-center justify-center text-center shadow-inner">
            <span class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-2">Returned</span>
            <span class="text-4xl font-black text-[#00df9a]">{{ $borrowTransaction->quantity_returned }}</span>
        </div>
        <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 flex flex-col items-center justify-center text-center shadow-inner">
            <span class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-2">Fine Amount</span>
            <span class="text-4xl font-black {{ $borrowTransaction->total_fine > 0 ? 'text-error animate-pulse' : 'text-[#00df9a]' }}">
                ₱{{ number_format($borrowTransaction->total_fine, 2) }}
            </span>
            @if($borrowTransaction->fine_amount > 0 && $borrowTransaction->status !== 'returned')
                <span class="text-[9px] font-bold text-white/30 mt-1">Incl. ₱{{ number_format($borrowTransaction->fine_amount, 2) }} locked</span>
            @endif
        </div>
    </div>

    @if ($borrowTransaction->status !== 'returned')
    <div class="bg-zinc-900/80 p-5 rounded-[2rem] border border-white/5 flex items-center justify-between shadow-2xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary/20 text-primary rounded-2xl flex items-center justify-center border border-primary/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[12px] font-black text-primary uppercase tracking-widest">Active Transaction</p>
                <p class="text-[10px] font-bold text-white/30">Complete return to avoid fines.</p>
            </div>
        </div>
        <button @click="fetchReturnForm('{{ route('borrow-transactions.edit', $borrowTransaction) }}')" 
                class="bg-white hover:bg-white/90 text-slate-900 font-extrabold px-10 py-3 rounded-2xl shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 uppercase tracking-widest text-[12px]">
            Return
        </button>
    </div>
    @else
    <div class="bg-success/5 p-5 rounded-[2rem] border border-success/20 flex items-center gap-4">
        <div class="w-12 h-12 bg-success text-white rounded-2xl flex items-center justify-center shadow-lg shadow-success/20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="text-[12px] font-black text-success uppercase tracking-widest">Record Completed</p>
            <p class="text-[10px] font-bold opacity-40">Returned on {{ $borrowTransaction->return_date->format('M d, Y') }}</p>
        </div>
    </div>
    @endif
</div>
</div>
