<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">{{ $student->name }}</h1>
                <p class="text-base-content/60 mt-2">Student Profile & Borrowing History</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline">Edit</a>
                <a href="{{ route('borrow-transactions.create') }}?student_id={{ $student->id }}" class="btn btn-primary">Borrow Book</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Info -->
            <div class="lg:col-span-2 bg-base-200 rounded-lg shadow-md p-8">
                <h2 class="text-xl font-bold mb-6">Student Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm opacity-50">Email</label>
                        <div class="font-semibold">{{ $student->email }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Phone</label>
                        <div class="font-semibold">{{ $student->phone ?? 'Not provided' }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Address</label>
                        <div class="font-semibold">{{ $student->address ?? 'Not provided' }}</div>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <label class="text-sm opacity-50">Member Since</label>
                        <div class="font-semibold">{{ $student->created_at->format('F d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-base-200 rounded-lg shadow-md p-8">
                <h2 class="text-xl font-bold mb-6">Activity Summary</h2>
                <div class="space-y-4">
                    <div>
                        <div class="text-3xl font-bold text-primary">
                            {{ $student->borrowTransactions()->whereIn('status', ['borrowed', 'partially_returned'])->count() }}
                        </div>
                        <div class="text-sm opacity-50">Active Borrows</div>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <div class="text-3xl font-bold text-error">
                            {{ $student->borrowTransactions()->whereIn('status', ['borrowed', 'partially_returned'])->where('due_date', '<', now())->count() }}
                        </div>
                        <div class="text-sm opacity-50">Overdue Books</div>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <div class="text-3xl font-bold text-warning">
                            ₱{{ number_format($student->borrowTransactions()->where('status', '!=', 'returned')->sum('fine_amount'), 2) }}
                        </div>
                        <div class="text-sm opacity-50">Outstanding Fines</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrowing History -->
        <div class="bg-base-200 rounded-lg shadow-md p-8">
            <h2 class="text-xl font-bold mb-6">Borrowing History</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Fine</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($borrowTransactions as $transaction)
                            <tr class="hover">
                                <td>{{ $transaction->book->title }}</td>
                                <td>{{ $transaction->borrow_date->format('M d, Y') }}</td>
                                <td>{{ $transaction->due_date->format('M d, Y') }}</td>
                                <td>{{ $transaction->return_date?->format('M d, Y') ?? '-' }}</td>
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
                                <td colspan="7" class="text-center py-12 text-base-content/60">No borrowing history</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $borrowTransactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
