<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10 shadow-xl">
            <div>
                <h1 class="text-4xl font-bold italic tracking-tighter">NEW <span class="text-primary">ADMIN</span></h1>
                <p class="text-lg text-white/60 mt-1 font-medium italic">Create a new system administrator account</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-ghost hover:bg-white/5 text-white/60 hover:text-white rounded-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </a>
        </div>

        <!-- Form Card -->
        <div class="glass-card rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden relative">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 blur-[120px] rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/5 blur-[100px] rounded-full"></div>

            <form action="{{ route('users.store') }}" method="POST" class="p-10 space-y-8 relative z-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Full Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Full Name</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-2xl h-14 text-white placeholder:text-white/10 transition-all font-bold text-lg @error('name') border-error/50 @enderror"
                            required placeholder="Ex: John Doe">
                        @error('name') <span class="text-error text-[10px] mt-2 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Email Address</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-2xl h-14 text-white placeholder:text-white/10 transition-all font-bold text-lg @error('email') border-error/50 @enderror"
                            required placeholder="admin@example.com">
                        @error('email') <span class="text-error text-[10px] mt-2 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8" x-data="{ showPw: false, showConfirm: false }">
                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Password</span>
                        </label>
                        <div class="relative">
                            <input :type="showPw ? 'text' : 'password'" name="password"
                                class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-2xl h-14 text-white placeholder:text-white/10 transition-all font-bold text-lg pr-14 @error('password') border-error/50 @enderror"
                                required placeholder="••••••••">
                            <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-5 flex items-center text-white/20 hover:text-primary transition-colors">
                                <svg x-show="!showPw" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPw" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('password') <span class="text-error text-[10px] mt-2 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Confirm Password</span>
                        </label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-2xl h-14 text-white placeholder:text-white/10 transition-all font-bold text-lg pr-14"
                                required placeholder="••••••••">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-5 flex items-center text-white/20 hover:text-primary transition-colors">
                                <svg x-show="!showConfirm" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showConfirm" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4 border-t border-white/5">
                    <a href="{{ route('users.index') }}" class="btn btn-ghost rounded-2xl px-10 text-white/40 hover:text-white hover:bg-white/5 transition-all h-14 font-black uppercase tracking-widest text-xs">Cancel</a>
                    <button type="submit" class="btn border-none bg-gradient-to-r from-primary to-primary-focus hover:scale-105 active:scale-95 text-white font-black uppercase tracking-[0.2em] text-xs rounded-2xl px-14 h-14 shadow-2xl shadow-primary/40 transition-all duration-300">
                        Create Administrator
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
