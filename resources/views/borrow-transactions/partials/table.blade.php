<div class="overflow-x-auto">
    <table class="table w-full text-white border-separate border-spacing-y-2">
        <thead class="bg-white/5 text-white border-b border-white/10">
            <tr>
                <th class="font-bold rounded-tl-lg">Student</th>
                <th class="font-bold">Book</th>
                <th class="font-bold">Borrow Date</th>
                <th class="font-bold">Due Date</th>
                <th class="font-bold">Fine</th>
                <th class="font-bold">Status</th>
                <th class="font-bold rounded-tr-lg">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr class="hover:bg-white/10 transition-colors glass-card">
                    <td class="font-bold text-white">{{ $transaction->student->name }}</td>
                    <td class="text-white/80 font-medium">{{ $transaction->book->title }}</td>
                    <td>{{ $transaction->borrow_date->format('M d, Y') }}</td>
                    <td class="{{ $transaction->due_date < now() && $transaction->status !== 'returned' ? 'text-error font-bold' : '' }}">
                        {{ $transaction->due_date->format('M d, Y') }}
                    </td>
                    <td>
                        @if ($transaction->fine_amount > 0)
                            <span class="text-error font-black">₱{{ number_format($transaction->fine_amount, 2) }}</span>
                        @else
                            <span class="text-white/30 italic">₱0.00</span>
                        @endif
                    </td>
                    <td>
                        @if ($transaction->status === 'borrowed')
                            <span class="badge badge-info">Borrowed</span>
                        @elseif ($transaction->status === 'partially_returned')
                            <span class="badge badge-warning">Partially Returned</span>
                        @else
                            <span class="badge badge-success">Returned</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1 border-white/10">
                            <button @click="fetchDetails('{{ route('borrow-transactions.show', $transaction) }}')" class="btn btn-sm btn-ghost hover:bg-info/20 text-white/70 hover:text-info transition-all duration-300 rounded-lg group" title="View Transaction Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-base-content/60">
                        <div class="space-y-2">
                            <div class="text-4xl">🧾</div>
                            <p class="font-semibold text-lg">No transactions found</p>
                            @if(!request('search'))
                                <button @click="showCreateModal = true" class="link link-primary font-semibold">Process your first loan</button> to get started
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-4 border-t border-base-200">
    <div class="flex justify-center">
        {{ $transactions->links() }}
    </div>
</div>
