<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Create Author</h1>
            <p class="text-base-content/60 mt-2">Add a new author to the system</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('authors.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Author Name *</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered @error('name') input-error @enderror" placeholder="Mark Twain" value="{{ old('name') }}" required>
                    @error('name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Bio</span>
                    </label>
                    <textarea name="bio" class="textarea textarea-bordered h-32" placeholder="Author biography...">{{ old('bio') }}</textarea>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('authors.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Author</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
