@extends('layouts.app')
@section('title', 'Add Author')

@section('content')
<div class="mb-6">
    <a href="{{ route('authors.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Authors</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Add New Author</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('authors.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @enderror" placeholder="Author name">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea name="bio" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('bio') border-red-400 @enderror" placeholder="Short biography (optional)">{{ old('bio') }}</textarea>
            @error('bio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">Save Author</button>
            <a href="{{ route('authors.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
