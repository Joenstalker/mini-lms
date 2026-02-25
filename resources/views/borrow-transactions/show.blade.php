<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">Transaction Details</h1>
                <p class="text-base-content/60 mt-2">Borrowing Record</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('books.index') }}" class="btn btn-ghost">Back to Catalog</a>
                @auth
                    @if ($borrowTransaction->status !== 'returned')
                        <a href="{{ route('borrow-transactions.edit', $borrowTransaction) }}" class="btn btn-primary px-8">
                            Return Books
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Transaction Info -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold mb-6">Transaction Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm opacity-50 mb-1">Student</label>
                            @auth
                                <a href="{{ route('students.show', $borrowTransaction->student) }}" class="link link-primary font-semibold block">
                                    {{ $borrowTransaction->student->name }}
                                </a>
                            @else
                                <a href="{{ route('borrow-transactions.student-history', $borrowTransaction->student) }}" class="link link-primary font-semibold block">
                                    {{ $borrowTransaction->student->name }}
                                </a>
                            @endauth
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Email</label>
                            <div class="font-medium">{{ $borrowTransaction->student->email }}</div>
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Book Title</label>
                            <a href="{{ route('books.show', $borrowTransaction->book) }}" class="link link-primary font-semibold block">
                                {{ $borrowTransaction->book->title }}
                            </a>
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Borrow Date</label>
                            <div>{{ $borrowTransaction->borrow_date->format('F d, Y g:i A') }}</div>
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Due Date</label>
                            <div {{ $borrowTransaction->due_date < now() && $borrowTransaction->status !== 'returned' ? 'class=text-error font-bold' : '' }}>
                                {{ $borrowTransaction->due_date->format('F d, Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Quantity Borrowed</label>
                            <div>{{ $borrowTransaction->quantity_borrowed }}</div>
                        </div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Quantity Returned</label>
                            <div>{{ $borrowTransaction->quantity_returned }}</div>
                        </div>
                    </div>
                    @if ($borrowTransaction->return_date)
                        <div class="divider"></div>
                        <div>
                            <label class="text-sm opacity-50 mb-1">Return Date</label>
                            <div>{{ $borrowTransaction->return_date->format('F d, Y g:i A') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fine & Status -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-lg font-bold mb-4">Status</h2>
                    <div class="mb-6">
                        @if ($borrowTransaction->status === 'borrowed')
                            <span class="badge badge-lg badge-info">CURRENTLY BORROWED</span>
                        @elseif ($borrowTransaction->status === 'partially_returned')
                            <span class="badge badge-lg badge-warning">PARTIALLY RETURNED</span>
                        @else
                            <span class="badge badge-lg badge-success">RETURNED</span>
                        @endif
                    </div>

                    @if ($borrowTransaction->due_date < now() && $borrowTransaction->status !== 'returned')
                        <div class="alert alert-error mb-4">
                            <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>This transaction is OVERDUE!</span>
                        </div>
                    @endif
                </div>

                <!-- Fine Card -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-lg font-bold mb-4">Fine Amount</h2>
                    <div class="mb-2">
                        <div class="text-4xl font-bold {{ $borrowTransaction->fine_amount > 0 ? 'text-error' : 'text-success' }}">
                            ₱{{ number_format($borrowTransaction->fine_amount, 2) }}
                        </div>
                    </div>
                    @if ($borrowTransaction->fine_amount > 0)
                        <div class="text-sm opacity-50">
                            <div>Calculation: ₱10 × days × books</div>
                            <div>Fine per day per book: ₱10.00</div>
                        </div>
                    @else
                        <div class="text-sm opacity-50">No fines</div>
                    @endif
                </div>

                <!-- Days Info -->
                <div class="bg-base-200 rounded-lg shadow-md p-8">
                    <h2 class="text-lg font-bold mb-4">Duration</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="opacity-50">Borrow Duration:</span>
                            <span class="font-semibold">{{ $borrowTransaction->borrow_date->diffInDays($borrowTransaction->due_date) }} days</span>
                        </div>
                        @if ($borrowTransaction->due_date < now())
                            <div>
                                <span class="opacity-50">Overdue Days:</span>
                                <span class="font-semibold text-error">{{ now()->diffInDays($borrowTransaction->due_date) }} days</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
