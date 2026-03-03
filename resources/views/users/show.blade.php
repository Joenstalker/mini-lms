<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="glass text-white rounded-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-white/10 shadow-xl">
            <div class="flex items-center gap-6">
                <div class="avatar shadow-2xl border-4 border-white/10 rounded-[2rem] overflow-hidden w-24 h-24 shrink-0">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" class="w-full h-full object-cover">
                    @else
                        <div class="bg-gradient-to-br from-primary to-primary-focus text-white w-full h-full flex items-center justify-center font-black text-4xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-4xl font-bold italic tracking-tighter">{{ strtoupper($user->name) }}</h1>
                    <p class="text-lg text-white/60 mt-1 font-medium italic">Administrator Profile</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary rounded-xl transition-all shadow-lg shadow-primary/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profile
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-ghost hover:bg-white/5 text-white/60 hover:text-white rounded-xl transition-all border border-white/5">
                    Back
                </a>
            </div>
        </div>

        <!-- Details Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="glass-card rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden relative p-10">
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 blur-[120px] rounded-full"></div>
                    
                    <h3 class="text-xl font-black uppercase tracking-widest text-white mb-8 border-b border-white/5 pb-4">Account Information</h3>
                    
                    <div class="space-y-8 relative z-10">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-primary shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Full Name</p>
                                <p class="text-xl font-bold text-white">{{ $user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-primary shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Email Address</p>
                                <p class="text-xl font-bold text-white">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-primary shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Account Created</p>
                                <p class="text-xl font-bold text-white">{{ $user->created_at->format('F d, Y') }} <span class="text-white/30 text-sm font-medium ml-2">({{ $user->created_at->diffForHumans() }})</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Status Card -->
                <div class="glass-card rounded-[2rem] p-8 border border-white/10 shadow-xl relative overflow-hidden">
                    <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-green-500/10 blur-[60px] rounded-full"></div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-4">Account Status</h4>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)] animate-pulse"></div>
                        <span class="text-lg font-black text-white uppercase tracking-widest">Active</span>
                    </div>
                </div>

                <!-- Role Card -->
                <div class="glass-card rounded-[2rem] p-8 border border-white/10 shadow-xl relative overflow-hidden">
                    <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-primary/10 blur-[60px] rounded-full"></div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-4">Role</h4>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="text-lg font-black text-white uppercase tracking-widest">System Admin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
