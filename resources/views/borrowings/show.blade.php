@extends('layouts.app')
@section('title', 'Borrowing Details')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('borrowings.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Borrowings</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Borrowing #{{ $borrowing->id }}</h1>
    </div>
    @if($borrowing->status !== 'returned')
    <form method="POST" action="{{ route('borrowings.return', $borrowing) }}" onsubmit="return confirm('Confirm return of this book?')">
        @csrf
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">✓ Mark as Returned</button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Borrowing Info -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Transaction Details</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between border-b border-gray-50 pb-2">
                <dt class="text-gray-500">Status</dt>
                <dd>
                    @if($borrowing->status === 'returned')
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Returned</span>
                    @elseif($borrowing->status === 'overdue')
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Overdue</span>
                    @else
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Borrowed</span>
                    @endif
                </dd>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-2">
                <dt class="text-gray-500">Borrowed On</dt>
                <dd class="font-medium text-gray-800">{{ $borrowing->borrowed_at->format('F d, Y') }}</dd>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-2">
                <dt class="text-gray-500">Due Date</dt>
                <dd class="font-medium {{ $borrowing->isOverdue() ? 'text-red-600' : 'text-gray-800' }}">{{ $borrowing->due_date->format('F d, Y') }}</dd>
            </div>
            @if($borrowing->returned_at)
            <div class="flex justify-between border-b border-gray-50 pb-2">
                <dt class="text-gray-500">Returned On</dt>
                <dd class="font-medium text-gray-800">{{ $borrowing->returned_at->format('F d, Y') }}</dd>
            </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-gray-500">Fine Amount</dt>
                <dd class="font-semibold {{ $fine > 0 ? 'text-red-600' : 'text-gray-800' }}">
                    @if($fine > 0)
                        ₱{{ number_format($fine, 2) }}
                    @else
                        No fine
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <!-- Student & Book Info -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Student</h2>
            <p class="font-semibold text-gray-800">{{ $borrowing->student->name }}</p>
            <p class="text-sm text-gray-500 font-mono">{{ $borrowing->student->student_number }}</p>
            @if($borrowing->student->email)
                <p class="text-sm text-gray-500 mt-1">{{ $borrowing->student->email }}</p>
            @endif
            <a href="{{ route('students.show', $borrowing->student) }}" class="text-indigo-600 hover:underline text-xs mt-2 inline-block">View profile →</a>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Book</h2>
            <p class="font-semibold text-gray-800">{{ $borrowing->book->title }}</p>
            <p class="text-sm text-gray-500">by {{ $borrowing->book->author->name }}</p>
            @if($borrowing->book->isbn)
                <p class="text-xs text-gray-400 mt-1">ISBN: {{ $borrowing->book->isbn }}</p>
            @endif
            <a href="{{ route('books.show', $borrowing->book) }}" class="text-indigo-600 hover:underline text-xs mt-2 inline-block">View book →</a>
        </div>
    </div>
</div>

@if($borrowing->isOverdue() && $borrowing->status !== 'returned')
<div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
    <p class="text-red-700 font-semibold text-sm">⚠️ This book is overdue!</p>
    <p class="text-red-600 text-sm mt-1">
        Overdue by {{ $borrowing->due_date->diffInDays(now()) }} day(s).
        Accrued fine: <strong>₱{{ number_format($fine, 2) }}</strong> (₱5.00/day)
    </p>
</div>
@endif
@endsection
