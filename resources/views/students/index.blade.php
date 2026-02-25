@extends('layouts.app')
@section('title', 'Students')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Students</h1>
        <p class="text-gray-500 text-sm mt-1">Manage registered students</p>
    </div>
    <a href="{{ route('students.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Add Student</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or student number..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 max-w-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">Search</button>
    @if(request('search'))
        <a href="{{ route('students.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @if($students->isEmpty())
        <p class="px-6 py-10 text-center text-gray-400">No students found. <a href="{{ route('students.create') }}" class="text-indigo-600 hover:underline">Add one now</a>.</p>
    @else
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Student #</th>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($students as $student)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $student->student_number }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    <a href="{{ route('students.show', $student) }}" class="hover:text-indigo-600">{{ $student->name }}</a>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $student->email ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $student->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('students.edit', $student) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium border border-indigo-200 px-2 py-1 rounded">Edit</a>
                        <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Delete this student?')">
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
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection
