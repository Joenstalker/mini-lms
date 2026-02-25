<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Create New Student</h1>
            <p class="text-base-content/60 mt-2">Add a new student to the library system</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('students.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Full Name *</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered @error('name') input-error @enderror" placeholder="John Doe" value="{{ old('name') }}" required>
                    @error('name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Email *</span>
                    </label>
                    <input type="email" name="email" class="input input-bordered @error('email') input-error @enderror" placeholder="john@example.com" value="{{ old('email') }}" required>
                    @error('email')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Phone</span>
                    </label>
                    <input type="text" name="phone" class="input input-bordered" placeholder="09xxxxxxxxx" value="{{ old('phone') }}">
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Address</span>
                    </label>
                    <textarea name="address" class="textarea textarea-bordered h-24" placeholder="Student address...">{{ old('address') }}</textarea>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('students.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Student</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
