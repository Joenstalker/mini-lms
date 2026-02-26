<div class="space-y-6">
    {{-- Student Header Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card p-6 rounded-[2.5rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-white">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h4 class="text-[9px] uppercase font-black tracking-[0.2em] text-white/40 mb-4">Personal Profile</h4>
            <div class="space-y-4">
                <div>
                    <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Full Name</span>
                    <span class="text-xl font-black text-primary tracking-tight">{{ $student->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Email Address</span>
                    <span class="font-bold text-sm text-white/80 italic">{{ $student->email }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Contact Phone</span>
                    <span class="font-bold text-sm text-white">{{ $student->phone ?? 'Not Provided' }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 rounded-[2.5rem] border border-white/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-white">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 0L10 18.9l-4.95-4.85zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h4 class="text-[9px] uppercase font-black tracking-[0.2em] text-white/40 mb-4">Location & Activity</h4>
            <div class="space-y-4">
                <div>
                    <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Registered Address</span>
                    <span class="font-bold text-sm text-white line-clamp-2 leading-relaxed">{{ $student->address ?? 'No address on file' }}</span>
                </div>
                <div class="flex gap-8">
                    <div>
                        <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Member Since</span>
                        <span class="font-bold text-sm text-white">{{ $student->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-white/30 block mb-1">Total Borrows</span>
                        <span class="font-bold text-sm text-white">{{ $student->borrowTransactions()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white/5 p-4 rounded-[1.8rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Active Borrows</span>
            <span class="text-2xl font-black text-primary">{{ $student->borrowTransactions()->whereIn('status', ['borrowed', 'partially_returned'])->count() }}</span>
        </div>
        <div class="bg-white/5 p-4 rounded-[1.8rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Overdue Records</span>
            <span class="text-2xl font-black text-error italic">{{ $student->borrowTransactions()->whereIn('status', ['borrowed', 'partially_returned'])->where('due_date', '<', now())->count() }}</span>
        </div>
        <div class="bg-white/5 p-4 rounded-[1.8rem] border border-white/10 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] uppercase font-black tracking-widest text-white/40 mb-1">Total Fines</span>
            <span class="text-2xl font-black text-warning">₱{{ number_format($student->borrowTransactions()->where('status', '!=', 'returned')->sum('fine_amount'), 2) }}</span>
        </div>
    </div>

    {{-- Borrowing History Table --}}
    <div class="glass-card rounded-[2rem] border border-white/10 overflow-hidden">
        <div class="bg-white/5 p-4 border-b border-white/10">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white/60">Borrowing History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full text-white">
                <thead class="bg-white/5 text-[10px] font-black uppercase tracking-widest text-white/40 border-b border-white/5">
                    <tr>
                        <th class="py-4">Book Title</th>
                        <th class="py-4">Date Borrowed</th>
                        <th class="py-4">Due Date</th>
                        <th class="py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($borrowTransactions as $transaction)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 font-bold text-sm">{{ $transaction->book->title }}</td>
                            <td class="py-4 text-sm opacity-60">{{ $transaction->borrow_date->format('M d, Y') }}</td>
                            <td class="py-4 text-sm font-bold {{ $transaction->due_date < now() && $transaction->status !== 'returned' ? 'text-error' : 'opacity-60' }}">
                                {{ $transaction->due_date->format('M d, Y') }}
                            </td>
                            <td class="py-4 text-center">
                                @if ($transaction->status === 'borrowed')
                                    <span class="badge badge-info badge-sm font-bold rounded-lg px-3">Borrowed</span>
                                @elseif ($transaction->status === 'partially_returned')
                                    <span class="badge badge-warning badge-sm font-bold rounded-lg px-3 text-[9px]">Partial</span>
                                @else
                                    <span class="badge badge-success badge-sm font-bold rounded-lg px-3">Returned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-white/30 italic font-medium">No borrowing history found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($borrowTransactions->hasPages())
            <div class="p-4 bg-white/5 border-t border-white/10">
                {{ $borrowTransactions->links() }}
            </div>
        @endif
    </div>
</div>
