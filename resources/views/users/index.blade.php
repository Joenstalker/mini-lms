<x-app-layout>
    <script>
        window._registerUsersDirectory = () => {
            Alpine.data('usersDirectory', (config) => ({
                showCreateModal: false,
                showEditModal: false,
                showDetailsModal: false,
                search: config.initialSearch,
                isLoading: false,
                detailsHtml: '',
                editData: { id: '', name: '', email: '' },
                showPassword: false,
                showConfirm: false,

                async openDetailsModal(userId) {
                    this.isLoading = true;
                    try {
                        const response = await fetch(`{{ url('users') }}/${userId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        this.detailsHtml = await response.text();
                        this.showDetailsModal = true;
                    } catch (error) {
                        console.error('Failed to load user details:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openEditModal(user) {
                    this.editData = { ...user };
                    this.showPassword = false;
                    this.showConfirm = false;
                    this.showEditModal = true;
                },

                async performSearch() {
                    this.isLoading = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        const response = await fetch(`{{ route('users.index') }}?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const html = await response.text();
                        const container = document.getElementById('users-table-content');
                        if (container) container.innerHTML = html;
                        window.history.replaceState(null, null, `?${params.toString()}`);
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

                    Swal.fire({
                        title: 'Creating Administrator...',
                        text: 'Please wait while we set up the new account.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: {
                            popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                            title: 'text-white font-bold',
                        }
                    });

                    try {
                        // artificial delay for visual consistency with other tabs
                        const delayPromise = new Promise(resolve => setTimeout(resolve, 2000));
                        const responsePromise = fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const [response] = await Promise.all([responsePromise, delayPromise]);
                        const result = await response.json();

                        if (response.ok) {
                            Swal.fire({
                                icon: 'success', title: 'Success!', text: result.message,
                                toast: true, position: 'top-end', showConfirmButton: false,
                                timer: 3000, timerProgressBar: true
                            });
                            this.showCreateModal = false;
                            form.reset();
                            await this.performSearch();
                        } else {
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            throw new Error(errorMsg || 'Creation failed');
                        }
                    } catch (error) {
                        Swal.fire({ 
                            icon: 'error', title: 'Registration Failed', text: error.message,
                            customClass: { popup: 'rounded-[1.5rem] bg-slate-900 border border-white/10 text-white' }
                        });
                    } finally {
                        this.isLoading = false;
                    }
                },

                async submitEdit(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    this.isLoading = true;

                    Swal.fire({
                        title: 'Updating Administrator...',
                        text: 'Saving changes to the account.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: {
                            popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                            title: 'text-white font-bold',
                        }
                    });

                    try {
                        const delayPromise = new Promise(resolve => setTimeout(resolve, 2000));
                        const responsePromise = fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const [response] = await Promise.all([responsePromise, delayPromise]);
                        const result = await response.json();

                        if (response.ok) {
                            Swal.fire({
                                icon: 'success', title: 'Updated!', text: result.message,
                                toast: true, position: 'top-end', showConfirmButton: false,
                                timer: 3000, timerProgressBar: true
                            });
                            this.showEditModal = false;
                            await this.performSearch();
                        } else {
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            throw new Error(errorMsg || 'Update failed');
                        }
                    } catch (error) {
                        Swal.fire({ 
                            icon: 'error', title: 'Update Failed', text: error.message,
                            customClass: { popup: 'rounded-[1.5rem] bg-slate-900 border border-white/10 text-white' }
                        });
                    } finally {
                        this.isLoading = false;
                    }
                },

                async confirmDelete(url) {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this administrator account!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff5861',
                        cancelButtonColor: '#355872',
                        confirmButtonText: 'Yes, delete it!',
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
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });

                            const data = await response.json();

                            if (response.ok) {
                                Swal.fire({
                                    icon: 'success', title: 'Deleted!', text: data.message,
                                    toast: true, position: 'top-end', showConfirmButton: false,
                                    timer: 3000, timerProgressBar: true
                                });
                                await this.performSearch();
                            } else {
                                throw new Error(data.message || 'Deletion failed');
                            }
                        } catch (error) {
                            Swal.fire({ 
                                icon: 'error', title: 'Error', text: error.message,
                                customClass: { popup: 'rounded-[1.5rem] bg-slate-900 border border-white/10 text-white' }
                            });
                        }
                    }
                }
            }));
        };

        document.addEventListener('alpine:init', window._registerUsersDirectory);
        if (window.Alpine) window._registerUsersDirectory();
    </script>

    <div class="space-y-6" x-data="usersDirectory({
        initialSearch: {{ json_encode($search ?? '') }}
    })">

        <!-- Header -->
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10">
            <div>
                <h1 class="text-4xl font-bold">Admin Accounts</h1>
                <p class="text-lg text-white/60 mt-2 font-medium">Manage all administrator accounts for this system</p>
            </div>

            <div class="flex-grow max-w-md w-full mx-0 md:mx-4">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <template x-if="!isLoading">
                            <svg class="h-5 w-5 text-base-content/30 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                        placeholder="Search by name or email..."
                        class="input input-bordered w-full pl-12 bg-base-100/80 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-2xl h-14 transition-all text-slate-800 font-bold"
                    >
                </div>
            </div>

            <button @click="showCreateModal = true" class="btn btn-ghost hover:bg-white/10 text-white rounded-xl gap-2 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="font-bold">Add Admin</span>
            </button>
        </div>

        <!-- Create Admin Modal -->
        <template x-teleport="body">
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-show="showCreateModal">
                <div class="modal-box max-w-xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 blur-[100px] rounded-full"></div>
                    <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">New Administrator</h3>
                            <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Registration Portal</p>
                        </div>
                        <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5 transition-all">✕</button>
                    </div>

                    <form action="{{ route('users.store') }}" method="POST" @submit.prevent="submitCreate($event)" class="flex flex-col flex-grow overflow-hidden">
                        @csrf
                        <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-4 scrollbar-thin relative z-10">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Full Name</span></label>
                                <input type="text" name="name" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold" required placeholder="Full Name">
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Email Address</span></label>
                                <input type="email" name="email" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold" required placeholder="admin@example.com">
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Password</span></label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold pr-12" required placeholder="••••••••">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/30 hover:text-primary transition-colors">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Confirm Password</span></label>
                                <div class="relative">
                                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold pr-12" required placeholder="••••••••">
                                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/30 hover:text-primary transition-colors">
                                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                            <button type="button" @click="showCreateModal = false" :disabled="isLoading" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
                            <button type="submit" :disabled="isLoading" class="btn border-none bg-gradient-to-r from-primary to-primary-focus hover:scale-105 active:scale-95 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-primary/20 transition-all duration-300">
                                <span x-show="isLoading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="!isLoading">Create Administrator</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Edit Admin Modal -->
        <template x-teleport="body">
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showEditModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-show="showEditModal">
                <div class="modal-box max-w-xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-warning/10 blur-[100px] rounded-full"></div>
                    <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">Edit Administrator</h3>
                            <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Update Account</p>
                        </div>
                        <button @click="showEditModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5 transition-all">✕</button>
                    </div>

                    <form :action="'{{ url('users') }}/' + editData.id" method="POST" @submit.prevent="submitEdit($event)" class="flex flex-col flex-grow overflow-hidden">
                        @csrf
                        @method('PATCH')
                        <div class="flex-grow overflow-y-auto p-8 pt-6 space-y-4 scrollbar-thin relative z-10">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Full Name</span></label>
                                <input type="text" name="name" x-model="editData.name" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold" required>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Email Address</span></label>
                                <input type="email" name="email" x-model="editData.email" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white transition-all font-bold" required>
                            </div>

                            <div class="bg-white/5 rounded-2xl p-4 border border-white/5 mt-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-warning mb-2">Security Update</p>
                                <p class="text-[10px] text-white/30 mb-4 italic">Leave blank to keep current password</p>
                                
                                <div class="space-y-4">
                                    <div class="form-control">
                                        <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">New Password</span></label>
                                        <div class="relative">
                                            <input :type="showPassword ? 'text' : 'password'" name="password" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold pr-12">
                                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/30 hover:text-warning transition-colors">
                                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <label class="label"><span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Confirm New Password</span></label>
                                        <div class="relative">
                                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" class="input w-full bg-white/5 border-white/10 focus:border-warning/50 focus:ring-4 focus:ring-warning/10 rounded-xl h-12 text-white placeholder:text-white/20 transition-all font-bold pr-12">
                                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/30 hover:text-warning transition-colors">
                                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                            <button type="button" @click="showEditModal = false" :disabled="isLoading" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
                            <button type="submit" :disabled="isLoading" class="btn border-none bg-gradient-to-r from-warning to-warning-focus hover:scale-105 active:scale-95 text-slate-900 font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-warning/20 transition-all duration-300">
                                <span x-show="isLoading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="!isLoading">Update Information</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Details Modal -->
        <template x-teleport="body">
            <div class="modal backdrop-blur-md" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;" x-show="showDetailsModal">
                <div class="modal-box max-w-2xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl relative overflow-hidden flex flex-col">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-info/10 blur-[100px] rounded-full"></div>
                    <div class="flex justify-between items-center p-8 pb-4 relative z-10 shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-md">
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">Admin Profile</h3>
                            <p class="text-[10px] text-white/40 mt-1 uppercase tracking-widest font-bold">Detailed Information</p>
                        </div>
                        <button @click="showDetailsModal = false" class="btn btn-sm btn-circle btn-ghost text-white/40 hover:text-white hover:bg-white/5 transition-all">✕</button>
                    </div>

                    <div class="flex-grow overflow-y-auto scrollbar-thin relative z-10">
                        <div x-html="detailsHtml"></div>
                    </div>

                    <div class="modal-action border-t border-white/10 p-8 pt-6 relative z-10 shrink-0 bg-white/5 backdrop-blur-md mt-0">
                        <button @click="showDetailsModal = false" class="btn border-none bg-white/10 hover:bg-white/20 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 transition-all duration-300">Close Profile</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Users Table -->
        <div class="glass-card rounded-2xl shadow-xl border border-white/10 overflow-hidden">
            <div class="p-8 flex items-center gap-3 border-b border-white/10">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">All Administrators</h3>
                    <p class="text-xs text-white/50">{{ $users->count() }} {{ Str::plural('account', $users->count()) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-lg">
                    <thead>
                        <tr class="bg-white/5 text-white/70">
                            <th class="py-4">Administrator</th>
                            <th>Joined</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-content">
                        @include('users.partials.table')
                    </tbody>
                </table>
            </div>
        </div>



        <script>
            document.addEventListener('click', function(e) {
                if (e.target.closest('.confirm-delete-user')) {
                    const button = e.target.closest('.confirm-delete-user');
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Remove Admin Account?',
                        text: "This admin will lose access to the system immediately.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff5861',
                        cancelButtonColor: '#355872',
                        confirmButtonText: 'Yes, remove them!',
                        customClass: {
                            popup: 'rounded-2xl border-none shadow-2xl',
                            confirmButton: 'rounded-xl',
                            cancelButton: 'rounded-xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData(form);
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(async (res) => {
                                const data = await res.json();
                                if (res.ok) {
                                    Swal.fire({
                                        icon: 'success', title: 'Removed!', text: data.message,
                                        toast: true, position: 'top-end', showConfirmButton: false,
                                        timer: 3000, timerProgressBar: true
                                    });
                                    const container = document.getElementById('users-table-content');
                                    if (container) {
                                        const params = new URLSearchParams(window.location.search);
                                        fetch(`{{ route('users.index') }}?${params.toString()}`, {
                                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                        }).then(r => r.text()).then(html => { container.innerHTML = html; });
                                    }
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                                }
                            })
                            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' }));
                        }
                    });
                }
            });
        </script>

    </div>
</x-app-layout>
