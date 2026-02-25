<x-guest-layout>
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-base-content">Welcome Back</h2>
        <p class="text-sm opacity-50 mt-1">Please enter your credentials to access the dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-bold uppercase tracking-wider text-[10px] opacity-60">Email Address</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-base-content/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 11-8 0 4 4 0 018 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                </div>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                    placeholder="name@example.com"
                    class="input input-bordered w-full pl-10 bg-base-200 border-base-300 focus:input-primary transition-none" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="form-control w-full">
            <div class="flex justify-between items-center mb-1">
                <label class="label py-0">
                    <span class="label-text font-bold uppercase tracking-wider text-[10px] opacity-60">Password</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold uppercase tracking-widest text-primary hover:underline" href="{{ route('password.request') }}">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-base-content/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="input input-bordered w-full pl-10 bg-base-200 border-base-300 focus:input-primary transition-none" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm rounded-md" />
                <span class="label-text text-xs opacity-60 font-medium whitespace-nowrap">Keep me logged in</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-primary btn-block rounded-xl">
                Sign In
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</x-guest-layout>
