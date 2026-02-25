<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Return Books</h1>
            <p class="text-base-content/60 mt-2">{{ $borrowTransaction->student->name }} - {{ $borrowTransaction->book->title }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Info Card -->
            <div class="bg-base-200 rounded-lg shadow-md p-8">
                <h2 class="text-xl font-bold mb-6">Transaction Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm opacity-50">Student</label>
                        <div class="font-semibold">{{ $borrowTransaction->student->name }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Book</label>
                        <div class="font-semibold">{{ $borrowTransaction->book->title }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Quantity Borrowed</label>
                        <div class="font-semibold">{{ $borrowTransaction->quantity_borrowed }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Quantity Already Returned</label>
                        <div class="font-semibold">{{ $borrowTransaction->quantity_returned }}</div>
                    </div>
                    <div>
                        <label class="text-sm opacity-50">Remaining to Return</label>
                        <div class="font-semibold text-primary">{{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}</div>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <label class="text-sm opacity-50">Due Date</label>
                        <div class="{{ $borrowTransaction->due_date < now() ? 'text-error font-bold' : 'font-semibold' }}">
                            {{ $borrowTransaction->due_date->format('F d, Y') }}
                        </div>
                    </div>
                    @if ($borrowTransaction->due_date < now())
                        <div class="alert alert-error">
                            <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>This transaction is overdue. Fine will be charged.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Return Form -->
            <div class="bg-base-200 rounded-lg shadow-md p-8">
                <form action="{{ route('borrow-transactions.update', $borrowTransaction) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <h2 class="text-xl font-bold">Return Books</h2>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Quantity to Return *</span>
                        </label>
                        <input 
                            type="number" 
                            name="quantity_returned" 
                            class="input input-bordered @error('quantity_returned') input-error @enderror" 
                            min="1"
                            max="{{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}"
                            value="{{ old('quantity_returned', 1) }}"
                            required
                        >
                        <label class="label">
                            <span class="label-text-alt">Max: {{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}</span>
                        </label>
                        @error('quantity_returned')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="alert alert-info">
                        <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="font-bold">Fine Calculation</span>
                            <div class="text-sm">₱10 per day per book</div>
                        </div>
                    </div>

                    <div class="flex gap-4 justify-end mt-8">
                        <a href="{{ route('borrow-transactions.show', $borrowTransaction) }}" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Record Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
