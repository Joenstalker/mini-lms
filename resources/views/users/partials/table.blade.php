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
            <div class="flex items-center justify-end gap-1">
                <button @click="openDetailsModal('{{ $user->id }}')" class="btn btn-sm btn-ghost hover:bg-white/10 text-white/50 hover:text-white transition-all duration-300 rounded-lg group" title="View Profile">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
                <button @click="openEditModal(JSON.parse($el.getAttribute('data-user')))"
                        data-user="{{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]) }}"
                        class="btn btn-sm btn-ghost hover:bg-white/10 text-white/50 hover:text-white transition-all duration-300 rounded-lg group" title="Edit Admin">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                @if($user->id !== Auth::id())
                    <button type="button" @click="confirmDelete('{{ route('users.destroy', $user) }}')" class="btn btn-sm btn-ghost hover:bg-error/20 hover:text-error text-white/50 transition-all duration-300 rounded-lg" title="Remove Admin">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                @else
                    <button class="btn btn-sm btn-ghost invisible" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-20 opacity-50">No admin accounts found</td>
    </tr>
@endforelse
