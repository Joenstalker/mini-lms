<x-guest-layout>
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-base-content">Reset Password</h2>
        <p class="text-sm opacity-50 mt-1">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="form-control w-full">
            <label class="label pt-0">
                <span class="label-text font-bold uppercase tracking-wider text-[10px] opacity-60">Email Address</span>
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-base-content/30 group-focus-within:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 11-8 0 4 4 0 018 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                </div>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    placeholder="name@example.com"
                    class="input input-bordered w-full pl-10 bg-base-200/50 focus:input-primary transition-all duration-300" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2 flex flex-col gap-3">
            <button type="submit" class="btn btn-primary btn-block shadow-lg shadow-primary/20">
                {{ __('Email Reset Link') }}
            </button>
            <a href="{{ route('login') }}" class="btn btn-ghost btn-block btn-sm opacity-60">Back to Login</a>
        </div>
    </form>
</x-guest-layout>
