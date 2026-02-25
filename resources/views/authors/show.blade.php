@extends('layouts.app')
@section('title', $author->name)

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('authors.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Authors</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $author->name }}</h1>
    </div>
    <a href="{{ route('authors.edit', $author) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Edit</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">About</h2>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $author->bio ?? 'No biography available.' }}</p>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Books by {{ $author->name }}</h2>
            </div>
            @if($author->books->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400">No books yet.</p>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">ISBN</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-center">Available</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($author->books as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">
                            <a href="{{ route('books.show', $book) }}" class="hover:text-indigo-600">{{ $book->title }}</a>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $book->isbn ?? '—' }}</td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $book->quantity }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="{{ $book->available_quantity > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $book->available_quantity }}</span>
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
