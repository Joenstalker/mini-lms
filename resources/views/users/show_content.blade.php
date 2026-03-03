<div class="space-y-6">
    <div class="flex items-center gap-6 p-6 glass-card rounded-3xl border border-white/10 bg-white/5">
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
            <h1 class="text-3xl font-black italic tracking-tighter text-white">{{ strtoupper($user->name) }}</h1>
            <p class="text-lg text-white/50 mt-1 font-medium italic">System Administrator</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-white/10 bg-white/5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Email Address</p>
                <p class="text-lg font-bold text-white">{{ $user->email }}</p>
            </div>
            
            <div class="glass-card rounded-2xl p-6 border border-white/10 bg-white/5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Member Since</p>
                <p class="text-lg font-bold text-white">{{ $user->created_at->format('F d, Y') }}</p>
                <p class="text-xs text-white/30">{{ $user->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-white/10 bg-white/5 relative overflow-hidden">
                <div class="absolute -bottom-6 -right-6 w-16 h-16 bg-green-500/10 blur-2xl rounded-full"></div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Account Status</p>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                    <span class="text-lg font-black text-white uppercase tracking-widest">Active</span>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-white/10 bg-white/5 relative overflow-hidden">
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-primary/10 blur-2xl rounded-full"></div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Role Permissions</p>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-lg font-black text-white uppercase tracking-widest">Administrator</span>
                </div>
            </div>
        </div>
    </div>
</div>
