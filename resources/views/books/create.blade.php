<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Add New Book</h1>
            <p class="text-base-content/60 mt-2">Create a new book entry in the library system</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('books.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Title *</span>
                    </label>
                    <input type="text" name="title" class="input input-bordered @error('title') input-error @enderror" placeholder="Enter book title" value="{{ old('title') }}" required>
                    @error('title')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">ISBN *</span>
                        </label>
                        <input type="text" name="isbn" class="input input-bordered @error('isbn') input-error @enderror" placeholder="19210-ISBN" value="{{ old('isbn') }}" required>
                        @error('isbn')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Publisher</span>
                        </label>
                        <input type="text" name="publisher" class="input input-bordered" placeholder="Publisher name" value="{{ old('publisher') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Published Year</span>
                        </label>
                        <input type="number" name="published_year" class="input input-bordered" placeholder="{{ date('Y') }}" value="{{ old('published_year') }}" min="1900" max="{{ date('Y') }}">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Total Quantity *</span>
                        </label>
                        <input type="number" name="total_quantity" class="input input-bordered @error('total_quantity') input-error @enderror" placeholder="Number of copies" value="{{ old('total_quantity') }}" min="1" required>
                        @error('total_quantity')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-32" placeholder="Book description...">{{ old('description') }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Authors</span>
                    </label>
                    <select name="authors[]" multiple class="select select-bordered select-multiple">
                        @forelse ($authors ?? [] as $author)
                            <option value="{{ $author->id }}" {{ in_array($author->id, old('authors', [])) ? 'selected' : '' }}>{{ $author->name }}</option>
                        @empty
                            <option disabled>No authors available</option>
                        @endforelse
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Hold Ctrl to select multiple authors</span>
                    </label>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('books.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Book</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
