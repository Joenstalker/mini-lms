<div class="space-y-6">
    {{-- Student Header Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card p-8 rounded-[3rem] border border-white/10 relative overflow-hidden group min-h-[320px] flex flex-col justify-between">
            {{-- Decorative background elements --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 blur-[100px] rounded-full -mr-32 -mt-32 transition-all duration-700 group-hover:bg-primary/20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-primary/5 blur-[60px] rounded-full -ml-16 -mb-16"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-start gap-8 relative z-10">
                <div class="space-y-6 flex-grow">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-primary/10 border border-primary/20 rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-primary shadow-sm">Digital ID</span>
                            <div class="h-px w-12 bg-white/10"></div>
                        </div>
                        <h4 class="text-[10px] uppercase font-black tracking-[0.3em] text-white/30 mb-2">Full Name</h4>
                        <div class="relative inline-block">
                            <span class="text-3xl md:text-4xl font-black text-white tracking-tighter leading-none block mb-2">{{ $student->name }}</span>
                            <div class="h-1.5 w-full bg-gradient-to-r from-primary/40 to-transparent rounded-full absolute -bottom-1 left-0"></div>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 p-3 px-5 rounded-2xl backdrop-blur-md">
                        <div>
                            <span class="text-[9px] font-black uppercase text-white/30 block">Member ID</span>
                            <span class="text-sm font-black text-primary font-mono tracking-wider">{{ $student->student_id }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="shrink-0 self-center md:self-start">
                    <div class="relative group/img">
                        {{-- Multi-layered glow --}}
                        <div class="absolute inset-0 bg-primary/30 blur-2xl rounded-[2rem] scale-90 opacity-0 group-hover/img:opacity-100 transition-opacity duration-700"></div>
                        <div class="absolute -inset-1 bg-gradient-to-br from-white/20 to-transparent rounded-[2.2rem] opacity-50"></div>
                        
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-[2rem] overflow-hidden border-2 border-white/20 shadow-2xl relative z-10 p-1 bg-white/5 backdrop-blur-sm">
                            <div class="w-full h-full rounded-[1.8rem] overflow-hidden">
                                @if($student->profile_image)
                                    <img src="{{ $student->profile_image }}" class="w-full h-full object-cover transform transition-transform duration-700 group-hover/img:scale-110">
                                @else
                                    <div class="w-full h-full bg-white/5 flex items-center justify-center text-white/10">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center gap-6 mt-8 relative z-10 pt-8 border-t border-white/5">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-white/5 flex items-center justify-center text-white/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <span class="text-[9px] font-black uppercase text-white/30 block">Email Address</span>
                        <span class="text-sm font-bold text-white/90 italic truncate block">{{ $student->email }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-white/5 flex items-center justify-center text-white/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <span class="text-[9px] font-black uppercase text-white/30 block">Contact Phone</span>
                        <span class="text-sm font-black text-white font-mono tracking-tighter truncate block">{{ $student->phone ?? '---' }}</span>
                    </div>
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
