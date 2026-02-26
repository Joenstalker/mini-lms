<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCreateModal: false,
        showDetailsModal: false,
        isLoadingDetails: false,
        detailsContent: '',
        countdown: { days: 0, hours: 0, mins: 0, secs: 0, isOverdue: false },
        timer: null,
        search: '{{ $search ?? '' }}',
        isLoading: false,
        async fetchDetails(url) {
            this.isLoadingDetails = true;
            this.showDetailsModal = true;
            this.detailsContent = '';
            if (this.timer) clearInterval(this.timer);
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.detailsContent = await response.text();
                
                // Extract due date and status from hidden inputs in partial
                this.$nextTick(() => {
                    const dueDateInput = document.getElementById('details-due-date');
                    const statusInput = document.getElementById('details-status');
                    if (dueDateInput && statusInput && statusInput.value !== 'returned') {
                        this.startCountdown(dueDateInput.value);
                    } else {
                        this.countdown.isOverdue = false; // Reset if already returned
                    }
                });
            } catch (error) {
                console.error('Fetch failed:', error);
                this.detailsContent = '<div class=\'alert alert-error\'>Failed to load transaction details.</div>';
            } finally {
                this.isLoadingDetails = false;
            }
        },
        startCountdown(dueStr) {
            const update = () => {
                const now = new Date().getTime();
                const due = new Date(dueStr).getTime();
                const diff = due - now;
                if (diff <= 0) {
                    this.countdown = { days: 0, hours: 0, mins: 0, secs: 0, isOverdue: true };
                    clearInterval(this.timer);
                    return;
                }
                this.countdown = {
                    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
                    hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                    mins: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
                    secs: Math.floor((diff % (1000 * 60)) / 1000),
                    isOverdue: false
                };
            };
            update();
            this.timer = setInterval(update, 1000);
        },
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

        <!-- Details Modal -->
        <div class="modal" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-5xl max-h-[90vh] rounded-[2.5rem] p-0 border border-white/10 shadow-2xl overflow-hidden flex flex-col">
                <div class="p-6 md:p-10 overflow-y-auto flex-grow custom-scrollbar">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-primary/10 rounded-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black tracking-tight text-base-content">Transaction Details</h3>
                                <p class="text-[10px] uppercase tracking-widest font-bold opacity-40">Borrowing Records System</p>
                            </div>
                        </div>
                        <button @click="showDetailsModal = false" class="btn btn-circle btn-ghost hover:rotate-90 transition-all duration-300">✕</button>
                    </div>
                    
                    <div x-show="isLoadingDetails" class="flex flex-col items-center justify-center py-16 space-y-6">
                        <div class="relative">
                            <span class="loading loading-spinner w-12 h-12 text-primary"></span>
                        </div>
                        <p class="text-lg font-bold opacity-60 animate-pulse uppercase tracking-[0.2em]">Synchronizing...</p>
                    </div>

                    <div x-show="!isLoadingDetails">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <!-- Left: Core Information (Static from fetch) -->
                            <div class="lg:col-span-8">
                                <div x-html="detailsContent"></div>
                            </div>

                            <!-- Right: Live Interactivity & Status -->
                            <div class="lg:col-span-4 space-y-4">
                                <!-- Live Countdown Card -->
                                <template x-if="countdown && !countdown.isOverdue && detailsContent.includes('details-active')">
                                    <div class="bg-primary/5 rounded-[2rem] p-5 border border-primary/10 shadow-inner">
                                        <h4 class="text-[9px] uppercase font-black tracking-[0.2em] text-primary mb-4 text-center opacity-60">Time Remaining</h4>
                                        <div class="grid grid-cols-4 gap-2">
                                            <div class="flex flex-col items-center">
                                                <div class="w-full aspect-square bg-base-100 rounded-xl flex items-center justify-center border border-base-300 shadow-sm">
                                                    <span class="text-xl font-black text-primary" x-text="countdown.days">0</span>
                                                </div>
                                                <span class="text-[7px] uppercase font-bold mt-1 opacity-40">Days</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <div class="w-full aspect-square bg-base-100 rounded-xl flex items-center justify-center border border-base-300 shadow-sm">
                                                    <span class="text-xl font-black text-primary" x-text="countdown.hours">0</span>
                                                </div>
                                                <span class="text-[7px] uppercase font-bold mt-1 opacity-40">Hrs</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <div class="w-full aspect-square bg-base-100 rounded-xl flex items-center justify-center border border-base-300 shadow-sm">
                                                    <span class="text-xl font-black text-primary" x-text="countdown.mins">0</span>
                                                </div>
                                                <span class="text-[7px] uppercase font-bold mt-1 opacity-40">Mins</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <div class="w-full aspect-square bg-base-100 rounded-xl flex items-center justify-center border border-base-300 shadow-sm">
                                                    <span class="text-xl font-black text-primary" x-text="countdown.secs">0</span>
                                                </div>
                                                <span class="text-[7px] uppercase font-bold mt-1 opacity-40">Secs</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Overdue Alert (Live) -->
                                <template x-if="countdown.isOverdue && detailsContent.includes('details-active')">
                                    <div class="bg-error/10 rounded-[2rem] p-5 border border-error/20 flex flex-col items-center text-center space-y-2 animate-pulse">
                                        <div class="w-10 h-10 bg-error text-white rounded-full flex items-center justify-center shadow-lg shadow-error/30">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-error font-black uppercase tracking-widest text-[10px]">Overdue Item</h4>
                                            <p class="text-[10px] font-bold opacity-60">Fines accumulating</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Quick Actions Section -->
                                <div id="modal-actions-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer for Actions -->
                <div class="px-8 py-4 bg-base-200/50 border-t border-base-300 flex justify-between items-center shrink-0">
                    <p class="text-[10px] opacity-40 italic">Live Sync: <span x-text="new Date().toLocaleTimeString()"></span></p>
                    <div class="flex gap-4">
                        <button @click="showDetailsModal = false" class="btn btn-ghost btn-md rounded-xl px-8 font-bold">Close Window</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-200 overflow-hidden">
            <div id="transactions-table-content">
                @include('borrow-transactions.partials.table', ['transactions' => $transactions])
            </div>
        </div>

    </div>
</x-app-layout>
