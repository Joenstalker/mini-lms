@extends('layouts.app')
@section('title', 'New Borrowing')

@section('content')
<div class="mb-6">
    <a href="{{ route('borrowings.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Borrowings</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">New Borrowing</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('borrowings.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
            <select name="student_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('student_id') border-red-400 @enderror">
                <option value="">Select student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->name }} ({{ $student->student_number }})
                    </option>
                @endforeach
            </select>
            @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Book <span class="text-red-500">*</span></label>
            <select name="book_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('book_id') border-red-400 @enderror">
                <option value="">Select available book</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}>
                        {{ $book->title }} — {{ $book->author->name }} ({{ $book->available_quantity }} available)
                    </option>
                @endforeach
            </select>
            @error('book_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Borrow Date <span class="text-red-500">*</span></label>
            <input type="date" name="borrowed_at" value="{{ old('borrowed_at', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('borrowed_at') border-red-400 @enderror">
            @error('borrowed_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
            <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('due_date') border-red-400 @enderror">
            @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-gray-400 text-xs mt-1">Default loan period is 14 days</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 text-xs text-yellow-700">
            <strong>Fine Policy:</strong> A fine of ₱5.00 per day is charged for overdue books.
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">Confirm Borrowing</button>
            <a href="{{ route('borrowings.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
