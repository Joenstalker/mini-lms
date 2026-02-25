<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Overdue Books</h1>
            <p class="text-base-content/60 mt-2">Books that are past their due date</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-base-300">
                        <tr>
                            <th>Student</th>
                            <th>Book</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Fine</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($overdueTransactions as $transaction)
                            <tr class="hover bg-red-50 dark:bg-red-950">
                                <td class="font-bold">{{ $transaction->student->name }}</td>
                                <td>{{ $transaction->book->title }}</td>
                                <td class="text-error font-bold">{{ $transaction->due_date->format('M d, Y') }}</td>
                                <td class="text-error font-bold">{{ now()->diffInDays($transaction->due_date) }}</td>
                                <td>
                                    @php
                                        $overdueDays = now()->diffInDays($transaction->due_date);
                                        $remainingQty = $transaction->quantity_borrowed - $transaction->quantity_returned;
                                        $calculatedFine = $overdueDays * 10 * $remainingQty;
                                    @endphp
                                    <span class="text-error font-bold">₱{{ number_format($calculatedFine, 2) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('borrow-transactions.show', $transaction) }}" class="btn btn-xs btn-ghost">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-base-content/60">
                                    <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    No overdue books! Everything is in order.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center">
            {{ $overdueTransactions->links() }}
        </div>
    </div>
</x-app-layout>
