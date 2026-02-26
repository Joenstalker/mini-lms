<!-- Data for Live Countdown -->
<input type="hidden" id="details-due-date" value="{{ $borrowTransaction->due_date->toIso8601String() }}">
<input type="hidden" id="details-status" value="{{ $borrowTransaction->status }}">
<div id="details-active" class="hidden"></div>

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Student Information -->
        <div class="glass-card p-5 rounded-[2rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity text-white">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h4 class="text-[9px] uppercase font-black tracking-[0.2em] text-white/40 mb-3">Student Information</h4>
            <div class="space-y-3">
                <div>
                    <span class="text-[10px] font-bold opacity-40 block">Full Name</span>
                    <span class="text-lg font-black text-primary tracking-tight">{{ $borrowTransaction->student->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-white/40 block">Email Address</span>
                    <span class="font-bold text-sm text-white/80 italic">{{ $borrowTransaction->student->email }}</span>
                </div>
            </div>
        </div>

        <!-- Book Information -->
        <div class="glass-card p-5 rounded-[2rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity text-white">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.434.29-3.48.804v10.009a8.808 8.808 0 012.89-.482c1.545 0 3.022.385 4.319 1.054V4.804zM10 14.817a8.59 8.59 0 011.604-.467c1.373-.323 2.801-.482 4.272-.482 1.546 0 3.022.385 4.319 1.054V4.804A7.968 7.968 0 0017.5 4c-1.255 0-2.434.29-3.48.804v10.009c.467-.226.96-.402 1.482-.526a8.59 8.59 0 00-1.482-.526V4.804z"></path>
                </svg>
            </div>
            <h4 class="text-[9px] uppercase font-black tracking-[0.2em] text-white/40 mb-3">Book Information</h4>
            <div class="space-y-3">
                <div>
                    <span class="text-[10px] font-bold opacity-40 block">Book Title</span>
                    <span class="text-lg font-black text-secondary tracking-tight line-clamp-1">{{ $borrowTransaction->book->title }}</span>
                </div>
                <div class="flex gap-5">
                    <div>
                        <span class="text-[10px] font-bold text-white/40 block">Borrowed</span>
                        <span class="font-bold text-sm text-white">{{ $borrowTransaction->borrow_date->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-white/40 block">Due Date</span>
                        <span class="font-bold text-sm text-white @if($borrowTransaction->due_date < now() && $borrowTransaction->status !== 'returned') text-error @endif">{{ $borrowTransaction->due_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & Timeline -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white/5 p-4 rounded-[1.5rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Quantity</span>
            <span class="text-2xl font-black text-white">{{ $borrowTransaction->quantity_borrowed }}</span>
        </div>
        <div class="bg-white/5 p-4 rounded-[1.5rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Returned</span>
            <span class="text-2xl font-black text-success">{{ $borrowTransaction->quantity_returned }}</span>
        </div>
        <div class="bg-white/5 p-4 rounded-[1.5rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Fine</span>
            <span class="text-2xl font-black {{ $borrowTransaction->fine_amount > 0 ? 'text-error animate-pulse' : 'text-success' }}">
                ₱{{ number_format($borrowTransaction->fine_amount, 2) }}
            </span>
        </div>
    </div>

    @if ($borrowTransaction->status !== 'returned')
    <div class="flex items-center gap-3 bg-primary/10 p-3 rounded-2xl border border-primary/20">
        <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </div>
        <div class="flex-grow">
            <p class="text-[11px] font-black text-primary uppercase tracking-tight">Active Transaction</p>
            <p class="text-[9px] font-bold opacity-60">Complete return to avoid fines.</p>
        </div>
        <a href="{{ route('borrow-transactions.edit', $borrowTransaction) }}" class="btn btn-primary btn-sm rounded-xl px-4 font-bold">
            Return
        </a>
    </div>
    @else
    <div class="flex items-center gap-3 bg-success/10 p-3 rounded-2xl border border-success/20">
        <div class="w-8 h-8 bg-success text-white rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="flex-grow">
            <p class="text-[11px] font-black text-success uppercase tracking-tight">Record Completed</p>
            <p class="text-[9px] font-bold opacity-60">Returned: {{ $borrowTransaction->return_date->format('M d, Y') }}</p>
        </div>
    </div>
    @endif
</div>
</div>
