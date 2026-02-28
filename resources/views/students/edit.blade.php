<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Edit Student</h1>
            <p class="text-base-content/60 mt-2">Update student information</p>
        </div>

        <div class="bg-base-200 rounded-lg shadow-md p-8 max-w-2xl">
            <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Full Name *</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered @error('name') input-error @enderror" placeholder="John Doe" value="{{ old('name', $student->name) }}" required>
                    @error('name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Student ID * (10 digits)</span>
                    </label>
                    <input type="text" name="student_id" class="input input-bordered @error('student_id') input-error @enderror" placeholder="2612345678" value="{{ old('student_id', $student->student_id) }}" required maxlength="10">
                    @error('student_id')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Email *</span>
                    </label>
                    <input type="email" name="email" class="input input-bordered @error('email') input-error @enderror" placeholder="john@example.com" value="{{ old('email', $student->email) }}" required>
                    @error('email')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Phone</span>
                    </label>
                    <input type="tel" name="phone" class="input input-bordered @error('phone') input-error @enderror" placeholder="09xxxxxxxxx" value="{{ old('phone', $student->phone) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Address</span>
                    </label>
                    <textarea name="address" class="textarea textarea-bordered h-24" placeholder="Student address...">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
