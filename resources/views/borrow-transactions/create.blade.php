<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Borrow a Book</h1>
            <p class="text-base-content/60 mt-2">Create a new borrowing transaction</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('borrow-transactions.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Student *</span>
                    </label>
                    <select name="student_id" class="select select-bordered @error('student_id') select-error @enderror" required>
                        <option value="" disabled selected>Select a student</option>
                        @foreach (\App\Models\Student::all() as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Book *</span>
                    </label>
                    <select name="book_id" class="select select-bordered @error('book_id') select-error @enderror" required>
                        <option value="" disabled selected>Select a book</option>
                        @foreach (\App\Models\Book::where('available_quantity', '>', 0)->with('authors')->get() as $book)
                            <option value="{{ $book->id }}" {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}>
                                {{ $book->title }} ({{ $book->available_quantity }} available)
                            </option>
                        @endforeach
                    </select>
                    @error('book_id')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Quantity to Borrow *</span>
                    </label>
                    <input type="number" name="quantity_borrowed" class="input input-bordered @error('quantity_borrowed') input-error @enderror" min="1" value="{{ old('quantity_borrowed', 1) }}" required>
                    @error('quantity_borrowed')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Due Date *</span>
                    </label>
                    <input type="date" name="due_date" class="input input-bordered @error('due_date') input-error @enderror" value="{{ old('due_date') }}" min="{{ now()->addDays(1)->format('Y-m-d') }}" required>
                    <label class="label">
                        <span class="label-text-alt">Fine of ₱10 per day per book for overdue books</span>
                    </label>
                    @error('due_date')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="alert alert-info">
                    <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Fine calculation: ₱10 × number of overdue days × number of books</span>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('borrow-transactions.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Record Borrow</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
