<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-secondary text-primary-content rounded-2xl shadow-2xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold">👥 Student Directory</h1>
                <p class="text-lg opacity-90 mt-2">Manage all student profiles and borrowing records</p>
            </div>
            <a href="{{ route('students.create') }}" class="btn btn-secondary btn-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4"></path>
                </svg>
                Add Student
            </a>
        </div>

        <!-- Students Table -->
        <div class="bg-gradient-to-br from-base-100 to-base-200 rounded-2xl shadow-lg border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gradient-to-r from-primary to-primary/80 text-primary-content sticky top-0">
                        <tr>
                            <th class="font-bold rounded-tl-lg">Name</th>
                            <th class="font-bold">Email Address</th>
                            <th class="font-bold">Phone</th>
                            <th class="text-center font-bold">Active Borrows</th>
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
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info btn-outline hover:btn-info transition-all duration-300">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                                        </a>
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning btn-outline hover:btn-warning transition-all duration-300">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-error btn-outline hover:btn-error transition-all duration-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
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
                                        <p class="font-semibold text-lg">No students yet</p>
                                        <p class="text-sm"><a href="{{ route('students.create') }}" class="link link-primary font-semibold">Add your first student</a> to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <div class="join">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
