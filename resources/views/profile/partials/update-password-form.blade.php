<section>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6"
        x-data="{
            loading: false,
            async submitForm(event) {
                this.loading = true;
                const formData = new FormData(event.target);
                try {
                    const response = await fetch('{{ route('password.update') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    
                    if (response.ok) {
                        event.target.reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Updated',
                            text: 'Your password has been successfully changed.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        const data = await response.json();
                        // For validation errors, the structure might be different
                        const errorMessage = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Something went wrong.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            }
        }" @submit.prevent="submitForm($event)">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button x-bind:disabled="loading">
                <span x-show="!loading">{{ __('Save') }}</span>
                <span x-show="loading" class="loading loading-spinner loading-sm"></span>
            </x-primary-button>
        </div>
    </form>
</section>
