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
        filter: '{{ $filter ?? 'all' }}',
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
            window.lastDetailsUrl = url;
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
                const response = await fetch(`{{ route('borrow-transactions.index') }}?search=${encodeURIComponent(this.search)}&filter=${this.filter}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                const container = document.getElementById('transactions-table-content');
                if (container) {
                    container.innerHTML = html;
                }
                window.history.replaceState(null, null, `?search=${encodeURIComponent(this.search)}&filter=${this.filter}`);
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
            this.isLoading = true;
            
            // Append selected books data
            this.selectedBooks.forEach((book, index) => {
                formData.append(`books[${index}][id]`, book.id);
                formData.append(`books[${index}][quantity]`, book.quantity);
            });

            Swal.fire({
                title: 'Processing Transaction...',
                text: 'Please wait while we finalize the borrowing record.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    title: 'text-white font-bold',
                }
            });

            try {
                const delayPromise = new Promise(resolve => setTimeout(resolve, 3000));
                const responsePromise = fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const [response] = await Promise.all([responsePromise, delayPromise]);
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
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Issue Failed', 
                    text: error.message,
                    customClass: {
                        popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    }
                });
            } finally {
                this.isLoading = false;
            }
        },
        async submitReturn(event) {
            const form = event.target;
            const formData = new FormData(form);
            this.isLoadingReturn = true;
            
            Swal.fire({
                title: 'Processing Return...',
                text: 'Please wait while we update the records.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    title: 'text-white font-bold',
                }
            });

            try {
                const delayPromise = new Promise(resolve => setTimeout(resolve, 3000));
                const responsePromise = fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const [response] = await Promise.all([responsePromise, delayPromise]);
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
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Return Failed', 
                    text: error.message,
                    customClass: {
                        popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    }
                });
            } finally {
                this.isLoadingReturn = false;
            }
        },
        async confirmDelete(url, status, studentName) {
            let title = 'Are you sure?';
            let text = 'You will not be able to recover this transaction!';
            let icon = 'warning';

            if (status === 'borrowed' || status === 'partially_returned') {
                title = 'Unreturned Books Detected!';
                text = `The book has not yet been returned by ${studentName}. Deleting this will restore the book stock. Proceed anyway?`;
                icon = 'error';
            }

            const result = await Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Keep it',
                background: '#1e293b',
                color: '#fff',
                customClass: {
                    popup: 'rounded-[2rem] border border-white/10 shadow-3xl backdrop-blur-xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: 'rgba(15, 23, 42, 0.95)',
                            color: '#fff'
                        });
                        await this.performSearch();
                    } else {
                        throw new Error(data.message || 'Deletion failed');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            }
        },
        async processReturn(url, remainingQty, fineAmount, bookTitle) {
            const isSettle = fineAmount > 0;
            const title = isSettle ? 'Settle Transaction?' : 'Return Book?';
            const icon = isSettle ? 'warning' : 'question';
            const confirmText = isSettle ? 'Yes, settle it!' : 'Yes, return it!';
            const text = isSettle 
                ? `This book is overdue. A fine of ₱${fineAmount.toFixed(2)} will be applied for ${remainingQty} units. This action cannot be undone.`
                : `Are you sure you want to return ${remainingQty} unit(s) of '${bookTitle}'?`;

            const result = await Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: isSettle ? '#f59e0b' : '#3085d6',
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel',
                background: '#0f172a',
                color: '#fff',
                customClass: {
                    popup: 'rounded-[2rem] border border-white/10 shadow-3xl backdrop-blur-xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-xs',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-xs'
                }
            });

            if (result.isConfirmed) {
                this.isLoadingReturn = true;
                Swal.fire({
                    title: 'Processing...',
                    text: isSettle ? 'Settling fines and updating records...' : 'Updating return status...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: '#0f172a',
                    color: '#fff',
                    customClass: { popup: 'rounded-[2rem] border border-white/10' }
                });

                try {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            quantity_returned: remainingQty
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: isSettle ? 'Settled!' : 'Returned!',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#0f172a',
                            color: '#fff'
                        });
                        
                        await this.performSearch();
                        
                        if (this.showDetailsModal) {
                            if (window.lastDetailsUrl) {
                                await this.fetchDetails(window.lastDetailsUrl);
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Processing failed');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        background: '#0f172a',
                        color: '#fff'
                    });
                } finally {
                    this.isLoadingReturn = false;
                }
            }
        }
    }">
    }">
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10 relative z-[60] overflow-visible">
            <div>
                <h1 class="text-4xl font-bold">Borrowing Transactions</h1>
                <p class="text-lg text-white/60 mt-2 font-medium">Manage all library borrowing records</p>
            </div>

            <div class="flex-grow max-w-2xl w-full mx-0 md:mx-4 flex items-center gap-4">
                {{-- Search Bar --}}
                <div class="relative group flex-grow">
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
                        @input.debounce.150ms="performSearch()"
                        placeholder="Search student ID, name, book or author..." 
                        class="input input-bordered w-full pl-12 bg-base-100/80 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-2xl h-14 transition-all text-slate-800 font-bold"
                    >
                </div>

                {{-- Status Filter Dropdown --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn h-14 min-h-[3.5rem] px-6 bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20 rounded-2xl flex items-center gap-3 transition-all">
                        <div class="flex flex-col items-start">
                            <span class="text-[9px] font-black uppercase tracking-widest text-primary/60 leading-none mb-1">Status</span>
                            <span class="text-xs font-black uppercase text-white truncate max-w-[80px]" x-text="filter.charAt(0).toUpperCase() + filter.slice(1).replace('_', ' ')"></span>
                        </div>
                        <svg class="w-4 h-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                    </label>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow-2xl bg-slate-900 border border-white/10 rounded-2xl w-48 mt-2 z-50 backdrop-blur-xl">
                        <li>
                            <button @click="filter = 'all'; performSearch()" class="flex items-center gap-3 py-3 rounded-xl hover:bg-white/5 transition-colors" :class="filter === 'all' ? 'bg-primary/10 text-primary' : 'text-white/60'">
                                <div class="w-2 h-2 rounded-full" :class="filter === 'all' ? 'bg-primary' : 'bg-white/20'"></div>
                                <span class="text-[11px] font-black uppercase tracking-widest">All Records</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'borrowed'; performSearch()" class="flex items-center gap-3 py-3 rounded-xl hover:bg-white/5 transition-colors" :class="filter === 'borrowed' ? 'bg-warning/10 text-warning' : 'text-white/60'">
                                <div class="w-2 h-2 rounded-full" :class="filter === 'borrowed' ? 'bg-warning' : 'bg-white/20'"></div>
                                <span class="text-[11px] font-black uppercase tracking-widest">Active Borrows</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'returned'; performSearch()" class="flex items-center gap-3 py-3 rounded-xl hover:bg-white/5 transition-colors" :class="filter === 'returned' ? 'bg-success/10 text-success' : 'text-white/60'">
                                <div class="w-2 h-2 rounded-full" :class="filter === 'returned' ? 'bg-success' : 'bg-white/20'"></div>
                                <span class="text-[11px] font-black uppercase tracking-widest">Fully Returned</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'overdue'; performSearch()" class="flex items-center gap-3 py-3 rounded-xl hover:bg-white/5 transition-colors" :class="filter === 'overdue' ? 'bg-error/10 text-error' : 'text-white/60'">
                                <div class="w-2 h-2 rounded-full" :class="filter === 'overdue' ? 'bg-error' : 'bg-white/20'"></div>
                                <span class="text-[11px] font-black uppercase tracking-widest">Overdue Items</span>
                            </button>
                        </li>
                    </ul>
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
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-transition x-show="showCreateModal">
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
                                    <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Borrow Date (Today)</span></label>
                                    <input type="date" name="borrow_date" class="input w-full bg-white/5 border-white/10 focus:ring-0 rounded-xl h-12 text-white/50 transition-all font-bold cursor-not-allowed" value="{{ date('Y-m-d') }}" readonly required>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Due Date</span></label>
                                    <input type="date" name="due_date" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                                </div>
                            </div>

                            {{-- Fine Policy Notice --}}
                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-4 flex items-start gap-4 transition-all hover:bg-blue-500/15">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0 text-blue-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Lending Policy Notice</h4>
                                    <p class="text-[10px] text-white/60 font-bold leading-relaxed uppercase tracking-wider">
                                        Standard fine of <span class="text-blue-400">₱10.00</span> per book will be charged for each day overdue.
                                    </p>
                                </div>
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
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-transition x-show="showDetailsModal">
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
                            {{-- Unified Full-Width Layout --}}
                            <div class="space-y-8">
                                <div x-html="btDetailsContent"></div>
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

        <div class="glass-card rounded-2xl shadow-xl border border-white/10 overflow-hidden">
            <div id="transactions-table-content" class="relative z-10">
                @include('borrow-transactions.partials.table', ['groupedTransactions' => $groupedTransactions])
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
