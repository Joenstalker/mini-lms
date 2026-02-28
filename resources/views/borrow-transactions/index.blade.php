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
        
        // Multi-book selection state
        bookSearch: '',
        selectedBooks: [], // Array of { id, title, available, quantity }
        availableBooks: @js(\App\Models\Book::where('available_quantity', '>', 0)->get()->map(fn($b) => ['id' => $b->id, 'title' => $b->title, 'available' => $b->available_quantity])),

        get filteredBooks() {
            if (!this.bookSearch) return this.availableBooks;
            const search = this.bookSearch.toLowerCase();
            return this.availableBooks.filter(b => b.title.toLowerCase().includes(search));
        },

        toggleBook(book) {
            const index = this.selectedBooks.findIndex(b => b.id === book.id);
            if (index > -1) {
                this.selectedBooks.splice(index, 1);
            } else {
                this.selectedBooks.push({ ...book, quantity: 1 });
            }
        },

        isBookSelected(id) {
            return this.selectedBooks.some(b => b.id === id);
        },

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
            if (this.selectedBooks.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Selection Empty', text: 'Please select at least one book to borrow.', confirmButtonColor: '#355872' });
                return;
            }

            const form = event.target;
            const formData = new FormData(form);
            
            // Append selected books data
            this.selectedBooks.forEach((book, index) => {
                formData.append(`books[${index}][id]`, book.id);
                formData.append(`books[${index}][quantity]`, book.quantity);
            });

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
                    this.selectedBooks = [];
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
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-show="showCreateModal">
                <div class="modal-box max-w-4xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                    {{-- Decorative background glow --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 blur-[100px] rounded-full"></div>
                    
                    {{-- Fixed Header --}}
                    <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">New Batch Transaction</h3>
                            <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Process Multiple Book Issues</p>
                        </div>
                        <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5">✕</button>
                    </div>

                    <form @submit.prevent="submitCreate($event)" action="{{ route('borrow-transactions.store') }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                        @csrf
                        
                        {{-- Scrollable Content Body --}}
                        <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-6 scrollbar-thin relative z-10">
                            {{-- Student Selection --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Select Student</span></label>
                                <select name="student_id" class="select w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" required>
                                    <option value="" disabled selected class="bg-slate-900">Choose a student</option>
                                    @foreach (\App\Models\Student::orderBy('name')->get() as $student)
                                        <option value="{{ $student->id }}" class="bg-slate-900 text-white">{{ $student->name }} ({{ $student->student_id }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Book Multi-Selection --}}
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <label class="label p-0"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Select Books & Quantities</span></label>
                                    <div class="relative w-64">
                                        <input type="text" x-model="bookSearch" placeholder="Search books..." class="input input-sm w-full bg-white/5 border-white/10 rounded-lg text-xs">
                                        <svg class="w-3 h-3 absolute right-3 top-2.5 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>

                                <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden">
                                    <div class="max-h-64 overflow-y-auto custom-scrollbar p-2 space-y-2">
                                        <template x-for="book in filteredBooks" :key="book.id">
                                            <div class="flex items-center gap-4 p-3 rounded-xl transition-all border border-transparent" :class="isBookSelected(book.id) ? 'bg-primary/10 border-primary/20' : 'hover:bg-white/5'">
                                                <label class="flex items-center gap-3 flex-grow cursor-pointer">
                                                    <input type="checkbox" :checked="isBookSelected(book.id)" @change="toggleBook(book)" class="checkbox checkbox-primary checkbox-sm rounded-md">
                                                    <div class="min-w-0">
                                                        <p class="font-bold text-sm truncate" x-text="book.title"></p>
                                                        <p class="text-[9px] uppercase tracking-widest font-bold opacity-40" x-text="`${book.available} Available`"></p>
                                                    </div>
                                                </label>
                                                
                                                <template x-if="isBookSelected(book.id)">
                                                    <div class="flex items-center gap-2 bg-white/5 p-1 px-2 rounded-lg border border-white/10">
                                                        <span class="text-[9px] font-black opacity-30 uppercase">Qty:</span>
                                                        <input type="number" 
                                                            x-model="selectedBooks.find(b => b.id === book.id).quantity" 
                                                            min="1" 
                                                            :max="book.available" 
                                                            class="w-12 bg-transparent border-none text-center font-black p-0 focus:ring-0 text-sm">
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="filteredBooks.length === 0">
                                            <div class="py-8 text-center opacity-30">
                                                <p class="text-xs font-black uppercase tracking-widest">No books found matching search</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
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
                    <div class="p-8 md:p-12 overflow-y-auto flex-grow custom-scrollbar">
                        <div class="flex justify-between items-start mb-10">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center border border-primary/20 shadow-lg shadow-primary/10 text-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black tracking-tight text-white">Transaction Details</h3>
                                    <p class="text-[11px] uppercase tracking-[0.3em] font-black opacity-40 mt-1">Borrowing Records System</p>
                                </div>
                            </div>
                            <button @click="showDetailsModal = false" class="btn btn-circle btn-ghost text-white/40 hover:text-white hover:rotate-90 transition-all duration-300">✕</button>
                        </div>
                        
                        <div x-show="isLoadingDetails" class="flex flex-col items-center justify-center py-20 space-y-6">
                            <div class="relative">
                                <span class="loading loading-spinner w-16 h-16 text-primary"></span>
                            </div>
                            <p class="text-xl font-black opacity-40 animate-pulse uppercase tracking-[0.3em]">Synchronizing...</p>
                        </div>

                        <div x-show="!isLoadingDetails">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                                <!-- Left: Core Information (Static from fetch) -->
                                <div class="lg:col-span-8">
                                    <div x-html="btDetailsContent"></div>
                                </div>

                                <!-- Right: Live Interactivity & Status -->
                                <div class="lg:col-span-4 h-full">
                                    <!-- Live Countdown Card -->
                                    <template x-if="countdown && !countdown.isOverdue && btDetailsContent.includes('details-active')">
                                        <div class="bg-white/5 rounded-[2.5rem] p-8 border border-white/10 shadow-2xl flex flex-col items-center justify-center h-full min-h-[220px]">
                                            <h4 class="text-[10px] uppercase font-black tracking-[0.3em] text-primary mb-8 opacity-60">Time Remaining</h4>
                                            <div class="grid grid-cols-4 gap-3 w-full">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                        <span class="text-2xl font-black text-slate-900" x-text="countdown.days">0</span>
                                                    </div>
                                                    <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Days</span>
                                                </div>
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                        <span class="text-2xl font-black text-slate-900" x-text="countdown.hours">0</span>
                                                    </div>
                                                    <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Hrs</span>
                                                </div>
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                        <span class="text-2xl font-black text-slate-900" x-text="countdown.mins">0</span>
                                                    </div>
                                                    <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Mins</span>
                                                </div>
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                        <span class="text-2xl font-black text-slate-900" x-text="countdown.secs">0</span>
                                                    </div>
                                                    <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Secs</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Overdue Alert (Live) -->
                                    <template x-if="countdown.isOverdue && btDetailsContent.includes('details-active')">
                                        <div class="bg-error/10 rounded-[2.5rem] p-8 border border-error/20 flex flex-col items-center text-center space-y-4 animate-pulse h-full justify-center min-h-[220px]">
                                            <div class="w-16 h-16 bg-error text-white rounded-3xl flex items-center justify-center shadow-2xl shadow-error/40">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-error font-black uppercase tracking-[0.3em] text-sm">Overdue Item</h4>
                                                <p class="text-[11px] font-bold opacity-40 mt-1">Fines are accumulating</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Footer for Actions -->
                    <div class="px-12 py-6 bg-white/5 border-t border-white/10 flex justify-between items-center shrink-0">
                        <p class="text-[11px] opacity-40 italic font-medium tracking-tight">Live Sync: <span x-text="new Date().toLocaleTimeString()"></span></p>
                        <button @click="showDetailsModal = false" class="text-white text-lg font-black hover:text-white/70 transition-colors tracking-tight">Close Window</button>
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

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
