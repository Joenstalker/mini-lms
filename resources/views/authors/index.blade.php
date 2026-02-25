<x-app-layout>
    <div class="space-y-6">
    <div class="space-y-6" x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        search: '{{ $search ?? '' }}',
        isLoading: false,
        editData: { id: '', name: '', bio: '' },
        openEditModal(author) {
            this.editData = { ...author };
            this.showEditModal = true;
        },
        async performSearch() {
            this.isLoading = true;
            try {
                const response = await fetch(`{{ route('authors.index') }}?search=${encodeURIComponent(this.search)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                document.getElementById('authors-grid-content').innerHTML = html;
                window.history.replaceState(null, null, `?search=${encodeURIComponent(this.search)}`);
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }">
        <!-- Header -->
        <div class="bg-base-200 text-base-content rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-base-300">
            <div>
                <h1 class="text-4xl font-bold">✍️ Authors Collection</h1>
                <p class="text-lg opacity-60 mt-2 font-medium">Browse and manage all book authors</p>
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
                        placeholder="Search name or bio..." 
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
                Add Author
            </button>
        </div>

        <!-- Authors Grid -->
        <div id="authors-grid-content">
            @include('authors.partials.table')
        </div>

        <!-- Create Modal -->
        <div class="modal" :class="{ 'modal-open': showCreateModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">New Author</h3>
                    <button @click="showCreateModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form action="{{ route('authors.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Author Name</span></label>
                        <input type="text" name="name" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" required placeholder="Author's full name">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Biography</span></label>
                        <textarea name="bio" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-xl h-32" placeholder="Brief biography of the author"></textarea>
                    </div>
                    <div class="modal-action mt-8">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-xl px-8">Save Author</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal" :class="{ 'modal-open': showEditModal }" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-box max-w-xl rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Edit Author</h3>
                    <button @click="showEditModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>
                <form :action="'{{ url('authors') }}/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Author Name</span></label>
                        <input type="text" name="name" x-model="editData.name" class="input input-bordered focus:input-primary bg-base-200 border-base-300 rounded-xl" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-[10px] uppercase tracking-widest opacity-60">Biography</span></label>
                        <textarea name="bio" x-model="editData.bio" class="textarea textarea-bordered focus:textarea-primary bg-base-200 border-base-300 rounded-xl h-32"></textarea>
                    </div>
                    <div class="modal-action mt-8">
                        <button type="button" @click="showEditModal = false" class="btn btn-ghost rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-xl px-8 text-warning-content">Update Author</button>
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
                        text: "All books by this author will remain, but the author profile will be removed!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff5861',
                        cancelButtonColor: '#355872',
                        confirmButtonText: 'Yes, delete author!',
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
