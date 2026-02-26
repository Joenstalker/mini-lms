<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCreateModal: {{ $errors->any() && !old('id') ? 'true' : 'false' }}, 
        showEditModal: {{ $errors->any() && old('id') ? 'true' : 'false' }},
        showDetailsModal: false,
        createPIN: '{{ old('pin') }}',
        editData: { 
            id: '{{ old('id') }}', 
            name: '{{ old('name') }}', 
            email: '{{ old('email') }}', 
            phone: '{{ old('phone') }}', 
            address: '{{ old('address') }}', 
            pin: '{{ old('pin') }}' 
        },
        detailsHtml: '',
        search: '{{ $search ?? '' }}',
        filter: '{{ request('filter') ?? '' }}',
        isLoading: false,
        async openDetailsModal(studentId) {
            this.isLoading = true;
            try {
                const response = await fetch(`{{ url('students') }}/${studentId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.detailsHtml = await response.text();
                this.showDetailsModal = true;
            } catch (error) {
                console.error('Failed to load student details:', error);
            } finally {
                this.isLoading = false;
            }
        },
        generatePIN(type) {
            const pin = Math.floor(100000 + Math.random() * 900000).toString();
            if (type === 'create') {
                this.createPIN = pin;
            } else {
                this.editData.pin = pin;
            }
        },
        openEditModal(student) {
            this.editData = { ...student };
            this.showEditModal = true;
        },
        async performSearch() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams();
                if (this.search) params.set('search', this.search);
                if (this.filter) params.set('filter', this.filter);
                
                const response = await fetch(`{{ route('students.index') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                document.getElementById('students-table-content').innerHTML = html;
                window.history.replaceState(null, null, `?${params.toString()}`);
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.isLoading = false;
            }
        },
        toggleFilter() {
            this.filter = this.filter === 'active' ? '' : 'active';
            this.performSearch();
        }
    }">
        <!-- Header -->
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10">
            <div>
                <h1 class="text-4xl font-bold">👥 Student Directory</h1>
                <p class="text-lg text-white/60 mt-2 font-medium">Manage all student profiles and borrowing records</p>
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
                        placeholder="Search name, email, or phone..." 
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

            <button @click="showCreateModal = true" class="btn btn-primary btn-lg rounded-xl shadow-md transition-all shrink-0">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Student
            </button>
        </div>

        <!-- Students Table -->
        <div class="glass-card rounded-2xl shadow-xl border border-white/10 overflow-hidden" id="students-table-content">
            @include('students.partials.table')
        </div>

        <div class="modal backdrop-blur-md" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.4)">
            <div class="modal-box max-w-xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                {{-- Decorative background glow --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 blur-[100px] rounded-full"></div>
                
                {{-- Fixed Header --}}
                <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">New Student</h3>
                        <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Registration Portal</p>
                    </div>
                    <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5">✕</button>
                </div>

                <form action="{{ route('students.store') }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                    @csrf
                    
                    {{-- Scrollable Content Body --}}
                    <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-4 scrollbar-thin relative z-10">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Full Name</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold @error('name') border-error/50 @enderror" required placeholder="Full Name">
                            @error('name') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Email Address</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold @error('email') border-error/50 @enderror" required placeholder="Email Address">
                            @error('email') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Phone Number</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold">
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Personal PIN (6 digits)</span>
                                <button type="button" @click="generatePIN('create')" class="label-text-alt text-primary hover:text-primary-focus transition-colors font-black uppercase text-[9px] tracking-widest">Generate</button>
                            </label>
                            <input type="text" name="pin" x-model="createPIN" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold @error('pin') border-error/50 @enderror" required placeholder="6 digits" minlength="4" maxlength="6">
                            @error('pin') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Address</span></label>
                            <textarea name="address" class="textarea bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-24 leading-relaxed text-sm py-4 text-white placeholder:text-white/20 transition-all" placeholder="Home address">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    {{-- Fixed Action Footer --}}
                    <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
                        <button type="submit" class="btn border-none bg-gradient-to-r from-primary to-primary-focus hover:scale-105 active:scale-95 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-primary/20 transition-all duration-300">
                            Save Student
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal backdrop-blur-md" :class="{ 'modal-open': showEditModal }" style="background-color: rgba(0,0,0,0.4)">
            <div class="modal-box max-w-xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                {{-- Decorative background glow --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-warning/10 blur-[100px] rounded-full"></div>
                
                {{-- Fixed Header --}}
                <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">Edit Student</h3>
                        <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Update Profile</p>
                    </div>
                    <button @click="showEditModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5">✕</button>
                </div>

                <form :action="'{{ url('students') }}/' + editData.id" method="POST" class="flex flex-col flex-grow overflow-hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" x-model="editData.id">
                    
                    {{-- Scrollable Content Body --}}
                    <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-4 scrollbar-thin relative z-10">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Full Name</span></label>
                            <input type="text" name="name" x-model="editData.name" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold @error('name') border-error/50 @enderror" required>
                            @error('name') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Email Address</span></label>
                            <input type="email" name="email" x-model="editData.email" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold @error('email') border-error/50 @enderror" required>
                            @error('email') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Phone Number</span></label>
                            <input type="text" name="phone" x-model="editData.phone" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold">
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Personal PIN (6 digits)</span>
                                <button type="button" @click="generatePIN('edit')" class="label-text-alt text-warning hover:text-warning-focus transition-colors font-black uppercase text-[9px] tracking-widest">Generate New</button>
                            </label>
                            <input type="text" name="pin" x-model="editData.pin" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold @error('pin') border-error/50 @enderror" required minlength="4" maxlength="6">
                            @error('pin') <span class="text-error text-[10px] mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Address</span></label>
                            <textarea name="address" x-model="editData.address" class="textarea bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-24 leading-relaxed text-sm py-4 text-white transition-all"></textarea>
                        </div>
                    </div>

                    {{-- Fixed Action Footer --}}
                    <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                        <button type="button" @click="showEditModal = false" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
                        <button type="submit" class="btn border-none bg-gradient-to-r from-warning to-warning-focus hover:scale-105 active:scale-95 text-warning-content font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-warning/20 transition-all duration-300">
                            Update Information
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal backdrop-blur-md" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.4)">
            <div class="modal-box max-w-4xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                {{-- Decorative background glow --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-info/10 blur-[100px] rounded-full"></div>
                
                {{-- Fixed Header --}}
                <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">Student Details</h3>
                        <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Member Information View</p>
                    </div>
                    <button @click="showDetailsModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5">✕</button>
                </div>

                {{-- Scrollable Content Body --}}
                <div class="flex-grow overflow-y-auto p-8 pt-6 scrollbar-thin relative z-10">
                    <div x-html="detailsHtml"></div>
                </div>

                {{-- Fixed Action Footer --}}
                <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                    <button type="button" @click="showDetailsModal = false" class="btn border-none bg-gradient-to-r from-slate-700 to-slate-800 hover:scale-105 active:scale-95 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-black/20 transition-all duration-300">
                        Close Profile
                    </button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('click', function(e) {
                if (e.target.closest('.confirm-delete')) {
                    const button = e.target.closest('.confirm-delete');
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this student profile!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff5861',
                        cancelButtonColor: '#355872',
                        confirmButtonText: 'Yes, delete it!',
                        customClass: {
                            popup: 'rounded-2xl border-none shadow-2xl',
                            confirmButton: 'rounded-xl',
                            cancelButton: 'rounded-xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        </script>

    </div>
</x-app-layout>
