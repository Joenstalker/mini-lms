@extends('layouts.app')
@section('title', 'Authors')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Authors</h1>
        <p class="text-gray-500 text-sm mt-1">Manage book authors</p>
    </div>
    <a href="{{ route('authors.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Add Author</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @if($authors->isEmpty())
        <p class="px-6 py-10 text-center text-gray-400">No authors yet. <a href="{{ route('authors.create') }}" class="text-indigo-600 hover:underline">Add one now</a>.</p>
    @else
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">#</th>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Bio</th>
                <th class="px-6 py-3 text-center">Books</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($authors as $author)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-400">{{ $author->id }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    <a href="{{ route('authors.show', $author) }}" class="hover:text-indigo-600">{{ $author->name }}</a>
                </td>
                <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $author->bio ?? '—' }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $author->books_count }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('authors.edit', $author) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium border border-indigo-200 px-2 py-1 rounded">Edit</a>
                        <form method="POST" action="{{ route('authors.destroy', $author) }}" onsubmit="return confirm('Delete this author?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium border border-red-200 px-2 py-1 rounded">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $authors->links() }}
    </div>
    @endif
</div>
@endsection
