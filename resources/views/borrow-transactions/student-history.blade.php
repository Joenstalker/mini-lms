<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center font-bold text-xl">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $student->name }}</h1>
                    <p class="text-base-content/60 font-medium tracking-tight">Borrowing History & Account Status</p>
                </div>
            </div>
            <a href="{{ route('books.index') }}" class="btn btn-ghost btn-sm rounded-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Catalog
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Summary Card -->
            <div class="bg-base-100 rounded-[2rem] shadow-xl p-8 border border-base-200">
                <h3 class="text-xs font-bold uppercase tracking-widest opacity-40 mb-6">Student Information</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Email Address</div>
                        <div class="text-base font-semibold">{{ $student->email }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Contact Number</div>
                        <div class="text-base font-semibold">{{ $student->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="pt-4 border-t border-base-200">
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Outstanding Fines</div>
                        <div class="text-2xl font-bold text-error">₱{{ number_format($student->outstanding_fine, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="lg:col-span-2 bg-base-100 rounded-[2rem] shadow-xl border border-base-200 overflow-hidden">
                <div class="p-8 border-b border-base-200 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Transaction History</h3>
                    <span class="badge badge-primary badge-outline font-bold">{{ $transactions->total() }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200/50">
                            <tr>
                                <th class="text-[10px] uppercase font-bold tracking-widest opacity-60">Book Title</th>
                                <th class="text-[10px] uppercase font-bold tracking-widest opacity-60">Borrowed</th>
                                <th class="text-[10px] uppercase font-bold tracking-widest opacity-60">Status</th>
                                <th class="text-[10px] uppercase font-bold tracking-widest opacity-60">Fine</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-primary/5 transition-colors">
                                    <td>
                                        <div class="font-bold text-base-content">{{ $transaction->book->title }}</div>
                                        <div class="text-[10px] opacity-40 uppercase tracking-tighter">Qty: {{ $transaction->quantity_borrowed }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium">{{ $transaction->borrow_date->format('M d, Y') }}</div>
                                        <div class="text-[10px] opacity-50">Due: {{ $transaction->due_date->format('M d, Y') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'borrowed' => 'badge-warning',
                                                'returned' => 'badge-success',
                                                'partially_returned' => 'badge-info',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$transaction->status] ?? 'badge-ghost' }} badge-sm font-bold uppercase text-[10px]">
                                            {{ str_replace('_', ' ', $transaction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-bold {{ $transaction->fine_amount > 0 ? 'text-error' : 'opacity-30' }}">
                                            ₱{{ number_format($transaction->fine_amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 opacity-50 italic">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-base-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
