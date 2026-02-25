<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Edit Book</h1>
            <p class="text-base-content/60 mt-2">Update book information</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('books.update', $book) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Title *</span>
                    </label>
                    <input type="text" name="title" class="input input-bordered @error('title') input-error @enderror" placeholder="Enter book title" value="{{ old('title', $book->title) }}" required>
                    @error('title')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">ISBN *</span>
                        </label>
                        <input type="text" name="isbn" class="input input-bordered @error('isbn') input-error @enderror" placeholder="19210-ISBN" value="{{ old('isbn', $book->isbn) }}" required>
                        @error('isbn')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Publisher</span>
                        </label>
                        <input type="text" name="publisher" class="input input-bordered" placeholder="Publisher name" value="{{ old('publisher', $book->publisher) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Published Year</span>
                        </label>
                        <input type="number" name="published_year" class="input input-bordered" placeholder="{{ date('Y') }}" value="{{ old('published_year', $book->published_year) }}" min="1900" max="{{ date('Y') }}">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Total Quantity *</span>
                        </label>
                        <input type="number" name="total_quantity" class="input input-bordered @error('total_quantity') input-error @enderror" placeholder="Number of copies" value="{{ old('total_quantity', $book->total_quantity) }}" min="1" required>
                        @error('total_quantity')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-32" placeholder="Book description...">{{ old('description', $book->description) }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Authors</span>
                    </label>
                    <select name="authors[]" multiple class="select select-bordered select-multiple">
                        @forelse (\App\Models\Author::all() as $author)
                            <option value="{{ $author->id }}" {{ $book->authors->contains($author->id) ? 'selected' : '' }}>{{ $author->name }}</option>
                        @empty
                            <option disabled>No authors available</option>
                        @endforelse
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Hold Ctrl to select multiple authors</span>
                    </label>
                </div>

                <div class="alert alert-info">
                    <svg class="stroke-current shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Current availability: {{ $book->available_quantity }}/{{ $book->total_quantity }}</span>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
