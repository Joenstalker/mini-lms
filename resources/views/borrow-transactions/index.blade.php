<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCreateModal: false,
        search: '{{ $search ?? '' }}',
        isLoading: false,
        async performSearch() {
            this.isLoading = true;
            try {
                const response = await fetch(`{{ route('borrow-transactions.index') }}?search=${encodeURIComponent(this.search)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                document.getElementById('transactions-table-content').innerHTML = html;
                window.history.replaceState(null, null, `?search=${encodeURIComponent(this.search)}`);
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }">
        <div class="bg-base-200 text-base-content rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-base-300">
            <div>
                <h1 class="text-4xl font-bold">Borrowing Transactions</h1>
                <p class="text-lg opacity-60 mt-2 font-medium">Manage all library borrowing records</p>
            </div>

            <div class="flex-grow max-w-md w-full mx-0 md:mx-4">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <template x-if="!isLoading">
                            <svg class="h-5 w-5 text-base-content/30 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </template>
                        <template x-if="isLoading">
                            <span class="loading loading-spinner loading-xs text-primary"></span>
                        </template>
                    </div>
                    <input 
                        type="text" 
                        x-model="search" 
                        @input.debounce.500ms="performSearch()"
                        placeholder="Search student or book..." 
                        class="input input-bordered w-full pl-12 bg-base-100/50 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-2xl h-14 transition-all"
                    >
                    <button 
                        x-show="search.length > 0" 
                        @click="search = ''; performSearch()" 
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-base-content/30 hover:text-error transition-colors"
                        style="display: none;"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button @click="showCreateModal = true" class="btn btn-primary btn-lg rounded-xl shadow-md transition-all gap-2 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Transaction
            </button>
        </div>

        <!-- Create Modal -->
        <div class="modal" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">New Transaction</h3>
                    <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form action="{{ route('borrow-transactions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Select Student</span></label>
                        <select name="student_id" class="select select-bordered focus:select-primary bg-base-200 border-base-300 rounded-xl" required>
                            <option value="" disabled selected>Choose a student</option>
                            @foreach (\App\Models\Student::all() as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Select Book</span></label>
                        <select name="book_id" class="select select-bordered focus:select-primary bg-base-200 border-base-300 rounded-xl" required>
                            <option value="" disabled selected>Choose a book</option>
                            @foreach (\App\Models\Book::where('available_quantity', '>', 0)->get() as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->available_quantity }} available)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Borrow Date</span></label>
                            <input type="date" name="borrow_date" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Due Date</span></label>
                            <input type="date" name="due_date" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Quantity</span></label>
                        <input type="number" name="quantity_borrowed" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" value="1" min="1" required>
                    </div>
                    <div class="modal-action mt-8">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-xl px-8">Process Loan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 text-base-content border-b border-base-300">
                        <tr>
                            <th class="font-bold">Student</th>
                            <th class="font-bold">Book</th>
                            <th class="font-bold">Borrow Date</th>
                            <th class="font-bold">Due Date</th>
                            <th class="font-bold">Fine</th>
                            <th class="font-bold">Status</th>
                            <th class="font-bold">Action</th>
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

    </div>
</x-app-layout>
