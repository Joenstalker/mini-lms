import './bootstrap';
import Alpine from 'alpinejs';
import * as Turbo from '@hotwired/turbo';

window.Alpine = Alpine;

// Make fetchAuthorDetails and openEditModal globally available
// These wrapper functions find the Alpine component and call its methods
window.fetchAuthorDetails = function (url) {
    const alpineElement = document.querySelector('[x-data]');
    if (alpineElement && Alpine.$data(alpineElement)) {
        const alpineData = Alpine.$data(alpineElement);
        if (alpineData.fetchAuthorDetails) {
            alpineData.fetchAuthorDetails(url);
        }
    }
};

window.openEditModal = function (author) {
    const alpineElement = document.querySelector('[x-data]');
    if (alpineElement && Alpine.$data(alpineElement)) {
        const alpineData = Alpine.$data(alpineElement);
        if (alpineData.openEditModal) {
            alpineData.openEditModal(author);
        }
    }
};

window.confirmLogout = async function (event) {
    event.preventDefault();
    const form = event.target.closest('form');

    const { isConfirmed } = await Swal.fire({
        title: 'Sign Out?',
        text: 'Are you sure you want to end your session?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff5861',
        cancelButtonColor: '#355872',
        confirmButtonText: 'Yes, Sign Out',
        cancelButtonText: 'Stay Logged In',
        customClass: {
            popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
            title: 'text-white font-black',
        }
    });

    if (isConfirmed) {
        Swal.fire({
            title: 'Signing out...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            customClass: {
                popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
            }
        });
        form.submit();
    }
};

// Start Turbo Drive - intercepts all link clicks and swaps the <body>
// without a full page reload.
Turbo.start();

// When Turbo loads a new page, we need to ensure Alpine is still running.
// We also restore the active link in the sidebar (which is data-turbo-permanent).
document.addEventListener('turbo:load', () => {
    // Update active nav link based on current URL
    const currentPath = window.location.pathname;
    document.querySelectorAll('#spa-sidebar nav a[href]').forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;
        const isActive = currentPath === linkPath || currentPath.startsWith(linkPath + '/');
        // Re-apply active class
        if (isActive) {
            link.classList.add('bg-primary', 'text-primary-content', 'shadow-lg', 'shadow-primary/20');
            link.classList.remove('hover:bg-primary/10', 'hover:text-primary');
        } else {
            link.classList.remove('bg-primary', 'text-primary-content', 'shadow-lg', 'shadow-primary/20');
            link.classList.add('hover:bg-primary/10', 'hover:text-primary');
        }
    });
});

// Register Author Management Component
Alpine.data('authorManagement', (config) => ({
    showCreateModal: false,
    showEditModal: false,
    showViewModal: false,
    search: config.search || '',
    isLoading: false,
    isLoadingView: false,
    imagePreview: null,
    viewContent: '',
    editData: { id: '', name: '', bio: '', profile_image: '' },

    async fetchAuthorDetails(url) {
        this.isLoadingView = true;
        this.showViewModal = true;
        this.viewContent = '';
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.viewContent = await response.text();
        } catch (error) {
            console.error('Fetch failed:', error);
            this.viewContent = '<div class="alert alert-error">Failed to load author profile.</div>';
        } finally {
            this.isLoadingView = false;
        }
    },

    openEditModal(author) {
        this.editData = { ...author };
        this.imagePreview = author.profile_image || null;
        this.showEditModal = true;
    },

    handleImageUpload(event, type) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
                if (type !== 'create') {
                    this.editData.profile_image = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    },

    async performSearch() {
        this.isLoading = true;
        try {
            const searchUrl = `${config.indexUrl}?search=${encodeURIComponent(this.search)}`;
            const response = await fetch(searchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            const container = document.getElementById('authors-grid-content');
            if (container) {
                container.innerHTML = html;
            }
            window.history.replaceState(null, null, `?search=${encodeURIComponent(this.search)}`);
        } catch (error) {
            console.error('Search failed:', error);
        } finally {
            this.isLoading = false;
        }
    },

    async submitAuthor(event, type) {
        const form = event.target;
        const formData = new FormData(form);

        Swal.fire({
            title: type === 'create' ? 'Creating Author...' : 'Updating Author...',
            text: 'Please wait while we finalize the profile.',
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const [response] = await Promise.all([responsePromise, delayPromise]);
            const result = await response.json();

            if (result.success) {
                if (type === 'create') this.showCreateModal = false;
                else this.showEditModal = false;

                await this.performSearch();

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
            } else {
                throw new Error(result.message || 'Something went wrong');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: error.message,
                customClass: {
                    popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                }
            });
        }
    },

    async confirmDelete(actionUrl, csrfToken) {
        const { isConfirmed } = await Swal.fire({
            title: 'Are you sure?',
            text: "All books by this author will remain, but the author profile will be removed!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5861',
            cancelButtonColor: '#355872',
            confirmButtonText: 'Yes, delete author!',
            customClass: {
                popup: 'rounded-2xl border-none shadow-2xl bg-slate-900/95 backdrop-blur-xl text-white border border-white/10',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        });

        if (isConfirmed) {
            try {
                Swal.fire({
                    title: 'Deleting Author...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    customClass: {
                        popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    }
                });

                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'DELETE');

                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    await this.performSearch();

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: result.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    customClass: {
                        popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
                    }
                });
            }
        }
    }
}));

// Start Alpine - only once. Turbo handles subsequent page transitions.
Alpine.start();
