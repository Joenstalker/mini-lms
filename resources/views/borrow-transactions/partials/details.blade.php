<div class="space-y-6">
    {{-- Top Header Section: Focused Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white/[0.03] p-8 rounded-[2.5rem] border border-white/10 flex flex-col items-center justify-center text-center shadow-inner relative overflow-hidden group hover:border-primary/30 transition-all duration-500">
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary/5 blur-[50px] rounded-full group-hover:bg-primary/10 transition-all"></div>
            <span class="text-[11px] uppercase font-black tracking-[0.4em] text-white/20 mb-3 relative z-10">Active Borrows</span>
            <span class="text-7xl font-black text-primary relative z-10 tracking-tighter drop-shadow-2xl">{{ $borrowTransactions->where('status', '!=', 'returned')->count() }}</span>
        </div>
        <div class="bg-white/[0.03] p-8 rounded-[2.5rem] border border-white/10 flex flex-col items-center justify-center text-center shadow-inner relative overflow-hidden group hover:border-error/30 transition-all duration-500">
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-error/5 blur-[50px] rounded-full group-hover:bg-error/10 transition-all"></div>
            <span class="text-[11px] uppercase font-black tracking-[0.4em] text-white/20 mb-3 relative z-10">Total Fines</span>
            <span class="text-7xl font-black text-error relative z-10 tracking-tighter drop-shadow-2xl {{ $student->total_fines > 0 ? 'animate-pulse' : '' }}">
                <span class="text-3xl text-error/50 mr-1 italic font-light">₱</span>{{ number_format($student->total_fines, 2) }}
            </span>
        </div>
    </div>

    {{-- Borrowing Details Matrix: Full Width Optimization --}}
    <div class="glass-card rounded-[3rem] border border-white/10 overflow-hidden shadow-2xl bg-slate-900/40 backdrop-blur-2xl w-full">
        <div class="bg-white/[0.03] p-10 border-b border-white/10 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <div class="w-12 h-12 bg-gradient-to-br from-[#ff2d60]/20 to-transparent rounded-2xl flex items-center justify-center border border-[#ff2d60]/20 text-[#ff2d60] shadow-lg shadow-[#ff2d60]/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black uppercase tracking-[0.4em] text-white/90">Borrowing Details Matrix</h3>
                    <p class="text-[10px] uppercase tracking-[0.2em] font-black text-white/20 mt-1 italic">Per-Book Performance & Compliance Tracking</p>
                </div>
            </div>
            <div class="px-5 py-2 bg-white/5 rounded-2xl border border-white/10">
                <span class="text-xs font-black text-white/40 uppercase tracking-widest">{{ $borrowTransactions->count() }} Historic Records</span>
            </div>
        </div>
        <div class="w-full">
            <table class="table w-full text-white border-separate border-spacing-y-3 px-6 pb-6 table-fixed">
                <thead class="bg-white/5 text-[11px] font-black uppercase tracking-[0.2em] text-white/30 border-b border-white/10">
                    <tr>
                        <th class="py-6 pl-8 rounded-l-3xl w-[30%] text-left">Book Title</th>
                        <th class="py-6 text-center w-[5%]">QTY</th>
                        <th class="py-6 text-center w-[12%]">Issue Date</th>
                        <th class="py-6 text-center w-[12%]">Deadline</th>
                        <th class="py-6 text-center w-[12%]">Fine</th>
                        <th class="py-6 text-center w-[14%] whitespace-nowrap">Status</th>
                        <th class="py-6 text-center pr-8 rounded-r-3xl w-[15%]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($borrowTransactions as $transaction)
                        <tr class="hover:bg-white/[0.04] transition-all group/row glass-card relative">
                            <td class="py-7 pl-8">
                                <div class="flex flex-col">
                                    <span class="font-black text-[13px] text-[#ff2d60] group-hover/row:text-[#ff477d] transition-all line-clamp-1 group-hover/row:translate-x-1 duration-300">{{ $transaction->book->title }}</span>
                                    <span class="text-[8px] uppercase font-bold text-white/10 mt-1 tracking-widest">ID: BT-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </td>
                            <td class="py-7 text-center">
                                <span class="px-3 py-1 bg-white/5 rounded-lg border border-white/10 text-[10px] font-black text-white/60 group-hover/row:text-primary transition-all">
                                    {{ $transaction->quantity_borrowed }}
                                </span>
                            </td>
                            <td class="py-7 text-xs font-black opacity-40 uppercase tabular-nums tracking-tighter text-center">{{ $transaction->borrow_date->format('M d, Y') }}</td>
                            <td class="py-7 text-xs font-black text-center {{ $transaction->is_overdue ? 'text-error' : 'opacity-40 uppercase tabular-nums tracking-tighter' }}">
                                {{ $transaction->due_date->format('M d, Y') }}
                            </td>
                            <td class="py-7 text-center">
                                <span class="text-sm font-black {{ $transaction->total_fine > 0 ? 'text-error' : 'text-success/40' }} tabular-nums">
                                    ₱{{ number_format($transaction->total_fine, 2) }}
                                </span>
                            </td>
                            <td class="py-7 text-center">
                                @php
                                    $statusClass = match($transaction->status) {
                                        'borrowed' => 'bg-info/20 text-info border-info/30',
                                        'partially_returned' => 'bg-warning/20 text-warning border-warning/30',
                                        default => 'bg-success/20 text-success border-success/30'
                                    };
                                @endphp
                                <span class="px-3 py-1.5 {{ $statusClass }} border rounded-xl text-[9px] font-black uppercase tracking-widest font-mono">
                                    {{ str_replace('_', ' ', $transaction->status) }}
                                </span>
                            </td>
                            <td class="py-7 text-center pr-8">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($transaction->status !== 'returned')
                                        @php
                                            $remainingQty = $transaction->quantity_borrowed - $transaction->quantity_returned;
                                            $isOverdue = $transaction->is_overdue;
                                            $fine = $transaction->total_fine;
                                        @endphp
                                        
                                        @if (!$isOverdue && $fine == 0)
                                            {{-- Return Button (No Fines) --}}
                                            <button @click="processReturn('{{ route('borrow-transactions.update', $transaction) }}', {{ $remainingQty }}, 0, '{{ addslashes($transaction->book->title) }}')" 
                                                    class="btn btn-xs border-none bg-success/10 hover:bg-success/20 text-success font-black rounded-xl px-3 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-xl shadow-success/5 uppercase tracking-widest text-[9px]">
                                                Return
                                            </button>
                                        @else
                                            {{-- Settle Button (Fines Applied) --}}
                                            <button @click="processReturn('{{ route('borrow-transactions.update', $transaction) }}', {{ $remainingQty }}, {{ $fine }}, '{{ addslashes($transaction->book->title) }}')" 
                                                    class="btn btn-xs border-none bg-white hover:bg-white/90 text-slate-900 font-black rounded-xl px-3 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-2xl shadow-white/5 uppercase tracking-widest text-[9px]">
                                                Settle
                                            </button>
                                        @endif
                                    @else
                                        <div class="inline-flex items-center justify-center gap-2 px-4 py-1.5 bg-white/5 rounded-xl border border-white/5 text-white/10 group-hover/row:text-white/20 transition-all">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span class="text-[8px] font-black uppercase italic tracking-widest">Done</span>
                                        </div>
                                    @endif

                                    {{-- Individual Delete Button --}}
                                    <button @click="confirmDelete('{{ route('borrow-transactions.destroy', $transaction) }}', '{{ $transaction->status }}', '{{ addslashes($transaction->student->name) }}')" 
                                            class="btn btn-xs btn-ghost text-white/20 hover:text-error hover:bg-error/10 rounded-xl px-2 transition-all duration-300" title="Delete Record">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-40">
                                <div class="flex flex-col items-center opacity-20">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 00-2 2H6a2 2 0 00-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <span class="text-lg font-black uppercase tracking-[0.5em]">Inventory Null</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
