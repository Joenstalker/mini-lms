<x-app-layout>
    <div class="space-y-6">

        @if ($borrowTransaction->status === 'partially_returned')
        <div class="alert alert-info">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Partial return: {{ $borrowTransaction->quantity_returned }} of {{ $borrowTransaction->quantity_borrowed }} copies already returned. Processing the remaining {{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}.</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Info Card -->
            <div class="bg-base-200 rounded-2xl shadow-md p-8 space-y-4">
                <h2 class="text-xl font-bold mb-2">Transaction Details</h2>

                <div>
                    <label class="text-xs font-bold uppercase tracking-widest opacity-50">Student</label>
                    <div class="font-semibold text-lg">{{ $borrowTransaction->student->name }}</div>
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-widest opacity-50">Book</label>
                    <div class="font-semibold text-lg">{{ $borrowTransaction->book->title }}</div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs opacity-50">Borrowed</label>
                        <div class="font-bold text-2xl">{{ $borrowTransaction->quantity_borrowed }}</div>
                    </div>
                    <div>
                        <label class="text-xs opacity-50">Returned</label>
                        <div class="font-bold text-2xl text-success">{{ $borrowTransaction->quantity_returned }}</div>
                    </div>
                    <div>
                        <label class="text-xs opacity-50">Still Out</label>
                        <div class="font-bold text-2xl text-warning">{{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}</div>
                    </div>
                </div>

                <div class="divider"></div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-widest opacity-50">Borrow Date</label>
                    <div class="font-semibold">{{ $borrowTransaction->borrow_date->format('F d, Y') }}</div>
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-widest opacity-50">Due Date</label>
                    <div class="{{ $borrowTransaction->due_date < now() ? 'text-error font-bold text-lg' : 'font-semibold' }}">
                        {{ $borrowTransaction->due_date->format('F d, Y') }}
                        @if ($borrowTransaction->due_date < now())
                            <span class="badge badge-error ml-2">{{ now()->diffInDays($borrowTransaction->due_date) }} days overdue</span>
                        @endif
                    </div>
                </div>

                @if ($borrowTransaction->due_date < now())
                    @php
                        $overdueDays = (int) now()->diffInDays($borrowTransaction->due_date);
                        $remaining   = $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned;
                        $currentFine = $overdueDays * 10 * $remaining;
                    @endphp
                    <div class="alert alert-error">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <div class="font-bold">Overdue Fine Due</div>
                            <div class="text-sm">₱10 × {{ $overdueDays }} days × {{ $remaining }} book(s) = <strong>₱{{ number_format($currentFine, 2) }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Return Form -->
            <div class="bg-base-200 rounded-2xl shadow-md p-8">
                <form action="{{ route('borrow-transactions.update', $borrowTransaction) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <h2 class="text-xl font-bold">Confirm Return</h2>
                        <p class="text-sm opacity-60 mt-1">Enter how many copies the student is returning right now.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-error text-sm">{{ $errors->first() }}</div>
                    @endif

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Quantity Returning *</span>
                        </label>
                        <input
                            type="number"
                            name="quantity_returned"
                            class="input input-bordered focus:input-primary bg-base-100 border-base-300 rounded-xl @error('quantity_returned') input-error @enderror"
                            min="1"
                            max="{{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}"
                            value="{{ old('quantity_returned', $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned) }}"
                            required
                        >
                        <label class="label">
                            <span class="label-text-alt opacity-50">Max returnable now: {{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}</span>
                        </label>
                    </div>

                    <div class="bg-base-100 rounded-xl p-4 border border-base-300 text-sm space-y-1">
                        <div class="font-bold text-xs uppercase tracking-widest opacity-60 mb-2">Fine Policy Reminder</div>
                        <div>Fine rate: <strong>₱10.00 per day, per book</strong></div>
                        <div>Applies only when returned after the due date.</div>
                        @if ($borrowTransaction->due_date < now())
                            @php $overdueDays = (int) now()->diffInDays($borrowTransaction->due_date); @endphp
                            <div class="text-error font-bold mt-2">Currently {{ $overdueDays }} day(s) overdue.</div>
                        @else
                            <div class="text-success font-semibold mt-2">Not yet overdue — no fine will be charged.</div>
                        @endif
                    </div>

                    <div class="flex gap-4 justify-end mt-8">
                        <a href="{{ route('borrow-transactions.show', $borrowTransaction) }}" class="btn btn-outline rounded-xl">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-xl px-8">
                            Confirm Return
                            @if ($borrowTransaction->due_date < now())
                                & Collect Fine
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
