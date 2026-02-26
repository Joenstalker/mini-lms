{{-- login.blade.php — content is rendered directly inside guest.blade.php --}}
<x-guest-layout>

    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-white text-center">Log in</h2>
        <div class="w-12 h-1 bg-primary mx-auto mt-4 rounded-full"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPassword: false }" onsubmit="showLoading('Signing you in...')">
        @csrf

        {{-- Email --}}
        <div class="form-control w-full">
            <label class="label pb-2">
                <span class="label-text">Email Address</span>
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-white/30 group-focus-within:text-sky-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M16 12a4 4 0 11-8 0 4 4 0 018 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" :value="old('email')"
                       required autofocus autocomplete="username"
                       placeholder="admin@library.com"
                       class="input input-bordered w-full pl-11 focus:border-sky-400 focus:outline-none transition-all duration-300 @error('email') border-error @enderror" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div class="form-control w-full">
            <div class="flex justify-between items-center mb-1">
                <label class="label py-0">
                    <span class="label-text">Password</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-[10px] font-bold uppercase tracking-widest text-sky-400 hover:text-sky-300 transition-colors">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-white/30 group-focus-within:text-sky-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="input input-bordered w-full pl-11 pr-11 focus:border-sky-400 focus:outline-none transition-all duration-300 @error('password') border-error @enderror" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/30 hover:text-sky-400 transition-colors">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember Me --}}
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3 py-0">
                <input type="checkbox" name="remember"
                       class="checkbox checkbox-primary checkbox-sm rounded-lg" />
                <span class="label-text text-xs opacity-50 font-medium">Keep me signed in</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button type="submit"
                    class="btn btn-primary w-full shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 transition-all duration-300">
                Sign In to Dashboard
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

        <div class="mt-8 text-center text-xs text-white/40 font-medium">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-sky-400 hover:text-sky-300 font-bold transition-colors">Sign up now</a>
        </div>
    </form>

</x-guest-layout>
