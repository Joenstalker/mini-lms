@forelse ($users as $user)
    <tr class="hover:bg-white/5 transition-colors border-b border-white/5">
        <td class="py-4">
            <div class="flex items-center gap-4">
                <div class="avatar shadow-sm border border-white/10 rounded-full overflow-hidden w-10 h-10 shrink-0">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" class="w-full h-full object-cover">
                    @else
                        <div class="bg-primary text-primary-content w-full h-full flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <div class="font-bold text-white flex items-center gap-2">
                        {{ $user->name }}
                        @if($user->id === Auth::id())
                            <span class="badge badge-primary badge-sm font-bold py-2 px-3 rounded-lg">You</span>
                        @endif
                    </div>
                    <div class="text-xs text-white/50">{{ $user->email }}</div>
                </div>
            </div>
        </td>
        <td>
            <div class="text-sm text-white/70 font-medium">{{ $user->created_at->format('M d, Y') }}</div>
            <div class="text-xs text-white/30">{{ $user->created_at->diffForHumans() }}</div>
        </td>
        <td class="text-right">
            @if($user->id !== Auth::id())
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-ghost hover:bg-error/20 hover:text-error text-white/50 transition-all duration-300 rounded-lg confirm-delete-user" title="Remove Admin">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            @else
                <span class="text-xs text-white/20 italic px-4">—</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-20 opacity-50">No admin accounts found</td>
    </tr>
@endforelse
