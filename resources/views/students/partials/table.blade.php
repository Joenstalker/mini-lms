<div class="overflow-x-auto">
    <table class="table table-zebra w-full">
        <thead class="bg-base-200 text-base-content border-b border-base-300">
            <tr>
                <th class="font-bold rounded-tl-lg">Name</th>
                <th class="font-bold">Email Address</th>
                <th class="font-bold">Phone</th>
                <th class="text-center font-bold cursor-pointer hover:bg-base-300 transition-colors group" @click="toggleFilter()">
                    <div class="flex items-center justify-center gap-2">
                        <span>Active Borrows</span>
                        <template x-if="filter === 'active'">
                            <span class="badge badge-primary badge-xs ring-2 ring-primary/20 animate-pulse"></span>
                        </template>
                        <svg class="w-4 h-4 opacity-30 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                </th>
                <th class="font-bold rounded-tr-lg">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr class="hover:bg-primary/10 transition-colors">
                    <td>
                        <div class="flex items-center gap-4">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-10 font-bold">
                                    <span>{{ substr($student->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="font-bold text-base">{{ $student->name }}</div>
                                <div class="text-sm opacity-70">Member since {{ $student->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="font-semibold text-base-content">{{ $student->email }}</div>
                    </td>
                    <td>
                        <span class="opacity-80">{{ $student->phone ?? '—' }}</span>
                    </td>
                    <td class="text-center">
                        @php $activeCount = $student->borrowTransactions->whereIn('status', ['borrowed', 'partially_returned'])->count(); @endphp
                        @if ($activeCount > 0)
                            <span class="badge badge-warning badge-lg font-bold">{{ $activeCount }}</span>
                        @else
                            <span class="badge badge-outline">0</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1 shrink-0">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-ghost hover:bg-info/20 hover:text-info transition-all duration-300 rounded-lg group" title="View Profile">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <button @click="openEditModal({
                                id: '{{ $student->id }}',
                                name: '{{ addslashes($student->name) }}',
                                email: '{{ addslashes($student->email) }}',
                                phone: '{{ addslashes($student->phone) }}',
                                address: '{{ addslashes($student->address) }}',
                                pin: '{{ $student->pin }}'
                            })" class="btn btn-sm btn-ghost hover:bg-warning/20 hover:text-warning transition-all duration-300 rounded-lg group" title="Edit Student">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-ghost hover:bg-error/20 hover:text-error transition-all duration-300 rounded-lg group confirm-delete" title="Delete Student">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-16 text-base-content/60">
                        <div class="space-y-2">
                            <div class="text-4xl">👤</div>
                            <p class="font-semibold text-lg">No students found</p>
                            @if(!request('search'))
                                <button @click="showCreateModal = true" class="link link-primary font-semibold">Add your first student</button> to get started
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t border-base-200">
        {{ $students->links() }}
    </div>
</div>
