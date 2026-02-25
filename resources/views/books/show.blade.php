@extends('layouts.app')
@section('title', $book->title)

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('books.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Books</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $book->title }}</h1>
        <p class="text-gray-500 text-sm">by {{ $book->author->name }}</p>
    </div>
    <div class="flex gap-2">
        @if($book->available_quantity > 0)
            <a href="{{ route('borrowings.create', ['book_id' => $book->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Borrow</a>
        @endif
        <a href="{{ route('books.edit', $book) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Edit</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Book Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">ISBN</dt><dd class="text-gray-800 font-medium">{{ $book->isbn ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Total Copies</dt><dd class="text-gray-800 font-medium">{{ $book->quantity }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Available</dt>
                    <dd class="{{ $book->available_quantity > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $book->available_quantity }}</dd>
                </div>
            </dl>
        </div>
        @if($book->description)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Description</h2>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $book->description }}</p>
        </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Borrowing History</h2>
            </div>
            @if($book->borrowings->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400">No borrowing records for this book.</p>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Student</th>
                        <th class="px-6 py-3 text-left">Borrowed</th>
                        <th class="px-6 py-3 text-left">Due</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($book->borrowings as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $b->student->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $b->borrowed_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $b->due_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($b->status === 'returned')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Returned</span>
                            @elseif($b->status === 'overdue')
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Overdue</span>
                            @else
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">Borrowed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
