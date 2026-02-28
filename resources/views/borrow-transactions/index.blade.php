<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCreateModal: false,
        showDetailsModal: false,
        showReturnModal: false,
        isLoadingDetails: false,
        isLoadingReturn: false,
        btDetailsContent: '',
        btReturnContent: '',
        countdown: { days: 0, hours: 0, mins: 0, secs: 0, isOverdue: false },
        timer: null,
        search: '{{ $search ?? '' }}',
        isLoading: false,
        async fetchDetails(url) {
            this.isLoadingDetails = true;
            this.showDetailsModal = true;
            this.btDetailsContent = '';
            if (this.timer) clearInterval(this.timer);
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.btDetailsContent = await response.text();
                
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
                this.btDetailsContent = '<div class=\'alert alert-error\'>Failed to load transaction details.</div>';
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
        async fetchReturnForm(url) {
            this.isLoadingReturn = true;
            this.showReturnModal = true;
            this.btReturnContent = '';
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.btReturnContent = await response.text();
            } catch (error) {
                console.error('Fetch failed:', error);
                this.btReturnContent = '<div class=\'alert alert-error\'>Failed to load return form.</div>';
            } finally {
                this.isLoadingReturn = false;
            }
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
        },
        async submitCreate(event) {
            const form = event.target;
            const formData = new FormData(form);
            this.isLoading = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    this.showCreateModal = false;
                    form.reset();
                    await this.performSearch();
                } else {
                    const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                    throw new Error(errorMsg || 'Process failed');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Issue Failed', text: error.message, confirmButtonColor: '#355872' });
            } finally {
                this.isLoading = false;
            }
        },
        async submitReturn(event) {
            const form = event.target;
            const formData = new FormData(form);
            this.isLoadingReturn = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Returned!',
                        text: result.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    this.showReturnModal = false;
                    await this.performSearch();
                } else {
                    const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                    throw new Error(errorMsg || 'Return failed');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Return Failed', text: error.message, confirmButtonColor: '#355872' });
            } finally {
                this.isLoadingReturn = false;
            }
        },
        confirmDelete(transactionId, status, studentName) {
            let title = 'Are you sure?';
            let text = 'You will not be able to recover this transaction!';
            let icon = 'warning';

            if (status === 'borrowed' || status === 'partially_returned') {
                title = 'Unreturned Books Detected!';
                text = `The book has not yet been returned by ${studentName}. Deleting this will restore the book stock. Proceed anyway?`;
                icon = 'error';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Keep it',
                background: '#1e293b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(`delete-form-${transactionId}`);
                    if (form) form.submit();
                }
            });
        }
    }">
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10">
            <div>
                <h1 class="text-4xl font-bold">Borrowing Transactions</h1>
                <p class="text-lg text-white/60 mt-2 font-medium">Manage all library borrowing records</p>
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

        <template x-teleport="body">
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;">
                <div class="modal-box max-w-xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                    {{-- Decorative background glow --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 blur-[100px] rounded-full"></div>
                    
                    {{-- Fixed Header --}}
                    <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">New Transaction</h3>
                            <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Process Book Issue</p>
                        </div>
                        <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5">✕</button>
                    </div>

                    <form @submit.prevent="submitCreate($event)" action="{{ route('borrow-transactions.store') }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                        @csrf
                        
                        {{-- Scrollable Content Body --}}
                        <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-4 scrollbar-thin relative z-10">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Select Student</span></label>
                                <select name="student_id" class="select w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" required>
                                    <option value="" disabled selected class="bg-slate-900">Choose a student</option>
                                    @foreach (\App\Models\Student::all() as $student)
                                        <option value="{{ $student->id }}" class="bg-slate-900 text-white">{{ $student->name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Select Book</span></label>
                                <select name="book_id" class="select w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" required>
                                    <option value="" disabled selected class="bg-slate-900">Choose a book</option>
                                    @foreach (\App\Models\Book::where('available_quantity', '>', 0)->get() as $book)
                                        <option value="{{ $book->id }}" class="bg-slate-900 text-white">{{ $book->title }} ({{ $book->available_quantity }} available)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Borrow Date</span></label>
                                    <input type="date" name="borrow_date" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Due Date</span></label>
                                    <input type="date" name="due_date" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Quantity</span></label>
                                <input type="number" name="quantity_borrowed" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" value="1" min="1" required>
                            </div>
                        </div>

                        {{-- Fixed Action Footer --}}
                        <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                            <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
                            <button type="submit" class="btn border-none bg-gradient-to-r from-primary to-primary-focus hover:scale-105 active:scale-95 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-primary/20 transition-all duration-300">
                                Process Issue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Details Modal -->
        <template x-teleport="body">
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;">
                <div class="modal-box max-w-5xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl overflow-hidden flex flex-col">
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
                                    <div x-html="btDetailsContent"></div>
                                </div>

                                <!-- Right: Live Interactivity & Status -->
                                <div class="lg:col-span-4 space-y-4">
                                    <!-- Live Countdown Card -->
                                    <template x-if="countdown && !countdown.isOverdue && btDetailsContent.includes('details-active')">
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
                                    <template x-if="countdown.isOverdue && btDetailsContent.includes('details-active')">
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
        </template>

        <template x-teleport="body">
            <!-- Return Modal -->
            <div class="modal modal-bottom sm:modal-middle backdrop-blur-md" :class="{ 'modal-open': showReturnModal }" style="background-color: rgba(0,0,0,0.6); z-index: 2000;">
                <div class="modal-box max-w-lg glass text-white rounded-[2.5rem] p-0 border border-white/20 shadow-2xl relative overflow-hidden">
                    {{-- Decorative background glow --}}
                    <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/20 blur-[100px] rounded-full"></div>
                    
                    <div class="p-8 relative z-10">
                        <div x-show="isLoadingReturn" class="flex flex-col items-center justify-center py-12 space-y-4">
                            <span class="loading loading-spinner loading-lg text-primary"></span>
                            <p class="text-[10px] uppercase font-black tracking-widest opacity-40 animate-pulse">Initializing Return Form...</p>
                        </div>

                        <div x-show="!isLoadingReturn" x-html="btReturnContent"></div>
                    </div>
                </div>
            </div>
        </template>

        <div class="glass-card rounded-2xl shadow-xl border border-white/10 overflow-hidden">
            <div id="transactions-table-content">
                @include('borrow-transactions.partials.table', ['transactions' => $transactions])
            </div>
        </div>

    </div>
</x-app-layout>
