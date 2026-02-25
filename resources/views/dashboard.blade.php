@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500 text-sm mt-1">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-full text-2xl">📖</div>
        <div>
            <p class="text-sm text-gray-500">Total Books</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalBooks }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
        <div class="bg-purple-100 text-purple-600 p-3 rounded-full text-2xl">✍️</div>
        <div>
            <p class="text-sm text-gray-500">Total Authors</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalAuthors }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
        <div class="bg-green-100 text-green-600 p-3 rounded-full text-2xl">🎓</div>
        <div>
            <p class="text-sm text-gray-500">Total Students</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
        <div class="bg-orange-100 text-orange-600 p-3 rounded-full text-2xl">🔄</div>
        <div>
            <p class="text-sm text-gray-500">Active Borrowings</p>
            <p class="text-2xl font-bold text-gray-800">{{ $activeBorrowings }}</p>
        </div>
    </div>
</div>

@if($overdueBorrowings > 0)
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
    <span class="text-2xl">⚠️</span>
    <div>
        <p class="font-semibold text-red-700">{{ $overdueBorrowings }} overdue borrowing(s) detected</p>
        <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}" class="text-red-600 hover:underline text-sm">View overdue records →</a>
    </div>
</div>
@endif

<!-- Recent Borrowings -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Recent Borrowings</h2>
        <a href="{{ route('borrowings.index') }}" class="text-indigo-600 hover:underline text-sm">View all →</a>
    </div>
    @if($recentBorrowings->isEmpty())
        <p class="px-6 py-8 text-center text-gray-400">No borrowing records yet.</p>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Student</th>
                    <th class="px-6 py-3 text-left">Book</th>
                    <th class="px-6 py-3 text-left">Borrowed</th>
                    <th class="px-6 py-3 text-left">Due</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentBorrowings as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $b->student->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $b->book->title }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $b->borrowed_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $b->due_date->format('M d, Y') }}</td>
                    <td class="px-6 py-3">
                        @if($b->status === 'returned')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Returned</span>
                        @elseif($b->status === 'overdue')
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Overdue</span>
                        @else
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Borrowed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="{{ route('books.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-3 text-center text-sm font-medium transition">+ Add Book</a>
    <a href="{{ route('authors.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-xl px-4 py-3 text-center text-sm font-medium transition">+ Add Author</a>
    <a href="{{ route('students.create') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-xl px-4 py-3 text-center text-sm font-medium transition">+ Add Student</a>
    <a href="{{ route('borrowings.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-xl px-4 py-3 text-center text-sm font-medium transition">+ Borrow Book</a>
</div>
@endsection
