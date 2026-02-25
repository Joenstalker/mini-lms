<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCreateModal: {{ $errors->any() && !old('id') ? 'true' : 'false' }}, 
        showEditModal: {{ $errors->any() && old('id') ? 'true' : 'false' }},
        createPIN: '{{ old('pin') }}',
        editData: { 
            id: '{{ old('id') }}', 
            name: '{{ old('name') }}', 
            email: '{{ old('email') }}', 
            phone: '{{ old('phone') }}', 
            address: '{{ old('address') }}', 
            pin: '{{ old('pin') }}' 
        },
        search: '{{ $search ?? '' }}',
        filter: '{{ request('filter') ?? '' }}',
        isLoading: false,
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
        <div class="bg-base-200 text-base-content rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-base-300">
            <div>
                <h1 class="text-4xl font-bold">👥 Student Directory</h1>
                <p class="text-lg opacity-60 mt-2 font-medium">Manage all student profiles and borrowing records</p>
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
        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-200 overflow-hidden" id="students-table-content">
            @include('students.partials.table')
        </div>

        <!-- Create Modal -->
        <div class="modal" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">New Student</h3>
                    <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Full Name</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('name') border-error @enderror" required placeholder="Full Name">
                        @error('name') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Email Address</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('email') border-error @enderror" required placeholder="Email Address">
                        @error('email') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Phone Number</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl">
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Personal PIN (6 digits)</span>
                            <button type="button" @click="generatePIN('create')" class="label-text-alt link link-primary font-bold">Generate PIN</button>
                        </label>
                        <input type="text" name="pin" x-model="createPIN" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('pin') border-error @enderror" required placeholder="Generate or type 6 digits" minlength="4" maxlength="6">
                        <label class="label"><span class="label-text-alt opacity-50 text-xs">A 6-digit number is recommended for security</span></label>
                        @error('pin') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Address</span></label>
                        <textarea name="address" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-xl h-20" placeholder="Student's home address">{{ old('address') }}</textarea>
                    </div>
                    <div class="modal-action mt-8">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-xl px-8">Save Student</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal" :class="{ 'modal-open': showEditModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Edit Student</h3>
                    <button @click="showEditModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form :action="'{{ url('students') }}/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" x-model="editData.id">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Full Name</span></label>
                        <input type="text" name="name" x-model="editData.name" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('name') border-error @enderror" required>
                        @error('name') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Email Address</span></label>
                        <input type="email" name="email" x-model="editData.email" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('email') border-error @enderror" required>
                        @error('email') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Phone Number</span></label>
                        <input type="text" name="phone" x-model="editData.phone" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl">
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Personal PIN (6 digits)</span>
                            <button type="button" @click="generatePIN('edit')" class="label-text-alt link link-primary font-bold">Generate New PIN</button>
                        </label>
                        <input type="text" name="pin" x-model="editData.pin" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl @error('pin') border-error @enderror" required minlength="4" maxlength="6">
                        @error('pin') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Address</span></label>
                        <textarea name="address" x-model="editData.address" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-xl h-20"></textarea>
                    </div>
                    <div class="modal-action mt-8">
                        <button type="button" @click="showEditModal = false" class="btn btn-ghost rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-xl px-8 text-warning-content">Update Information</button>
                    </div>
                </form>
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
