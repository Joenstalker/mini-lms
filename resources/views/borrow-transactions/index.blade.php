<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">Borrowing Transactions</h1>
                <p class="text-base-content/60 mt-2">Manage all library borrowing records</p>
            </div>
            <a href="{{ route('borrow-transactions.create') }}" class="btn btn-primary gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Transaction
            </a>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-base-300">
                        <tr>
                            <th>Student</th>
                            <th>Book</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Fine</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr class="hover">
                                <td class="font-bold">{{ $transaction->student->name }}</td>
                                <td>{{ $transaction->book->title }}</td>
                                <td>{{ $transaction->borrow_date->format('M d, Y') }}</td>
                                <td class="{{ $transaction->due_date < now() && $transaction->status !== 'returned' ? 'text-error font-bold' : '' }}">
                                    {{ $transaction->due_date->format('M d, Y') }}
                                </td>
                                <td>
                                    @if ($transaction->fine_amount > 0)
                                        <span class="text-error font-bold">₱{{ number_format($transaction->fine_amount, 2) }}</span>
                                    @else
                                        <span class="opacity-50">₱0.00</span>
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
                                    <a href="{{ route('borrow-transactions.show', $transaction) }}" class="btn btn-xs btn-ghost">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/60">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
