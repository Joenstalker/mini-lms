@extends('layouts.app')
@section('title', 'Borrowings')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Borrowings</h1>
        <p class="text-gray-500 text-sm mt-1">Track book borrow and return transactions</p>
    </div>
    <a href="{{ route('borrowings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ New Borrowing</a>
</div>

<!-- Filters -->
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student or book..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 max-w-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All Status</option>
        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">Filter</button>
    @if(request('search') || request('status'))
        <a href="{{ route('borrowings.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @if($borrowings->isEmpty())
        <p class="px-6 py-10 text-center text-gray-400">No borrowing records found.</p>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Student</th>
                    <th class="px-6 py-3 text-left">Book</th>
                    <th class="px-6 py-3 text-left">Borrowed</th>
                    <th class="px-6 py-3 text-left">Due Date</th>
                    <th class="px-6 py-3 text-left">Returned</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-right">Fine</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($borrowings as $borrowing)
                <tr class="hover:bg-gray-50 {{ $borrowing->status === 'overdue' ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $borrowing->student->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $borrowing->book->title }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $borrowing->borrowed_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600 {{ $borrowing->status === 'overdue' ? 'text-red-600 font-semibold' : '' }}">{{ $borrowing->due_date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $borrowing->returned_at ? $borrowing->returned_at->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-3 text-center">
                        @if($borrowing->status === 'returned')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Returned</span>
                        @elseif($borrowing->status === 'overdue')
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Overdue</span>
                        @else
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Borrowed</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right">
                        @if($borrowing->fine_amount > 0)
                            <span class="text-red-600 font-medium">₱{{ number_format($borrowing->fine_amount, 2) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('borrowings.show', $borrowing) }}" class="text-gray-600 hover:text-gray-800 text-xs border border-gray-200 px-2 py-1 rounded">View</a>
                            @if($borrowing->status !== 'returned')
                            <form method="POST" action="{{ route('borrowings.return', $borrowing) }}" onsubmit="return confirm('Mark as returned?')">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 text-xs border border-green-200 px-2 py-1 rounded">Return</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('borrowings.destroy', $borrowing) }}" onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs border border-red-200 px-2 py-1 rounded">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $borrowings->links() }}
    </div>
    @endif
</div>
@endsection
