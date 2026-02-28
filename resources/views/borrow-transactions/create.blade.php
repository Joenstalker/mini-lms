<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Borrow a Book</h1>
            <p class="text-base-content/60 mt-2">Create a new borrowing transaction</p>
        </div>

        <div class="bg-base-100 rounded-[2rem] shadow-xl p-8 max-w-2xl border border-base-200">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-success/10 text-success rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">New Borrow Transaction</h1>
                    <p class="text-base-content/60 font-medium">Select the borrower and the book being checked out at the counter</p>
                </div>
            </div>

            <form action="{{ route('borrow-transactions.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Student / Borrower *</span>
                    </label>
                    <select name="student_id" class="select select-bordered focus:select-primary bg-base-200 border-base-300 rounded-xl w-full" required>
                        <option value="" disabled selected>Search by student name</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                                @if ($student->borrowTransactions->whereIn('status', ['borrowed','partially_returned'])->count())
                                    — ({{ $student->borrowTransactions->whereIn('status', ['borrowed','partially_returned'])->count() }} active borrowing{{ $student->borrowTransactions->whereIn('status', ['borrowed','partially_returned'])->count() > 1 ? 's' : '' }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <label class="label"><span class="label-text-alt opacity-50">Choose the student who is borrowing</span></label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Select Book *</span>
                    </label>
                    <select name="book_id" class="select select-bordered focus:select-primary bg-base-200 border-base-300 rounded-xl w-full" required>
                        <option value="" disabled selected>Choose a book from the catalog</option>
                        @foreach ($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}>
                                {{ $book->title }} — {{ $book->available_quantity }} available
                                @if ($book->authors->count()) ({{ $book->authors->pluck('name')->join(', ') }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Number of Copies *</span>
                        </label>
                        <input type="number" name="quantity_borrowed" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" min="1" value="{{ old('quantity_borrowed', 1) }}" required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Return Due Date *</span>
                        </label>
                        <input type="date" name="due_date" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" min="{{ now()->addDays(1)->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10 space-y-3">
                    <div class="flex items-center gap-3 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="font-bold text-sm uppercase tracking-wider">Library Policy</h4>
                    </div>
                    <p class="text-sm opacity-70 leading-relaxed">Overdue books will incur a fine of <span class="font-bold text-primary">₱10.00 per day per book</span>. Please ensure timely returns to avoid penalties.</p>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('borrow-transactions.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Record Borrow</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
