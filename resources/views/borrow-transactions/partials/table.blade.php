<div class="overflow-x-auto">
    <table class="table w-full text-white border-separate border-spacing-y-2">
        <thead class="bg-white/5 text-white border-b border-white/10">
            <tr>
                <th class="font-bold rounded-tl-lg">Student</th>
                <th class="font-bold">Total Borrows</th>
                <th class="font-bold">Active Borrows</th>
                <th class="font-bold">Total Fines</th>
                <th class="font-bold">Last Activity</th>
                <th class="font-bold rounded-tr-lg">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedTransactions as $group)
                <tr class="hover:bg-white/10 transition-colors glass-card">
                    <td class="font-bold text-white">
                        <div class="flex items-center gap-3">
                            <div class="avatar shadow-sm border border-white/10 rounded-full overflow-hidden w-10 h-10">
                                @if($group->student->profile_image)
                                    <img src="{{ Str::startsWith($group->student->profile_image, ['http', 'data:']) ? $group->student->profile_image : '/images/' . $group->student->profile_image }}" class="w-full h-full object-cover">
                                @else
                                    <div class="bg-primary text-primary-content w-full h-full flex items-center justify-center font-bold text-sm">
                                        {{ substr($group->student->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-black">{{ $group->student->name }}</div>
                                <div class="text-[10px] opacity-40 uppercase tracking-widest">{{ $group->student->student_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-white/60 font-bold text-xs">{{ $group->total_count }} Records</td>
                    <td class="text-center">
                        @if ($group->active_count > 0)
                            <span class="badge badge-warning font-black text-[10px] px-3">{{ $group->active_count }} Active</span>
                        @else
                            <span class="badge badge-outline text-white/20 text-[10px] px-3">0 Active</span>
                        @endif
                    </td>
                    <td>
                        @if ($group->student->total_fines > 0)
                            <span class="text-error font-black">₱{{ number_format($group->student->total_fines, 2) }}</span>
                        @else
                            <span class="text-white/30 italic">₱0.00</span>
                        @endif
                    </td>
                    <td class="text-xs text-white/40 italic">
                        {{ \Carbon\Carbon::parse($group->last_transaction_at)->diffForHumans() }}
                    </td>
                    <td>
                        <div class="flex gap-1 border-white/10">
                            {{-- We use the last transaction ID as a proxy to open the student's history modal --}}
                            @php 
                                $proxyTransaction = \App\Models\BorrowTransaction::where('student_id', $group->student_id)->latest()->first();
                            @endphp
                            <button @click="fetchDetails('{{ route('borrow-transactions.show', $proxyTransaction) }}')" class="btn btn-sm btn-ghost hover:bg-info/20 text-white/70 hover:text-info transition-all duration-300 rounded-lg group" title="View Student History">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button @click="confirmDelete('{{ route('borrow-transactions.destroy-group', $groupedTransactions->find($group->student_id) ? $group->student_id : $group->student_id) }}', '{{ $group->active_count > 0 ? 'borrowed' : 'returned' }}', '{{ addslashes($group->student->name) }}')" 
                                    class="btn btn-sm btn-ghost hover:bg-error/20 text-white/70 hover:text-error transition-all duration-300 rounded-lg group" title="Delete All History">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-base-content/60">
                        <div class="space-y-2">
                            <div class="text-4xl">🧾</div>
                            <p class="font-semibold text-lg">No grouped transactions found</p>
                            @if(!request('search'))
                                <button @click="showCreateModal = true" class="link link-primary font-semibold">Process your first borrowing</button> to get started
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-4 border-t border-white/5">
    <div class="flex justify-center">
        {{ $groupedTransactions->links() }}
    </div>
</div>
