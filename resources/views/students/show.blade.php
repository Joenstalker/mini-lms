@extends('layouts.app')
@section('title', $student->name)

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('students.index') }}" class="text-indigo-600 hover:underline text-sm">← Back to Students</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $student->name }}</h1>
        <p class="text-gray-500 text-sm font-mono">{{ $student->student_number }}</p>
    </div>
    <a href="{{ route('students.edit', $student) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Edit</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Contact Info</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-gray-500 text-xs">Email</dt><dd class="text-gray-800">{{ $student->email ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Phone</dt><dd class="text-gray-800">{{ $student->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Registered</dt><dd class="text-gray-800">{{ $student->created_at->format('M d, Y') }}</dd></div>
            </dl>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Borrowing History</h2>
            </div>
            @if($student->borrowings->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400">No borrowing records.</p>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Book</th>
                        <th class="px-6 py-3 text-left">Borrowed</th>
                        <th class="px-6 py-3 text-left">Due</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Fine</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($student->borrowings as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">
                            <a href="{{ route('borrowings.show', $b) }}" class="hover:text-indigo-600">{{ $b->book->title }}</a>
                        </td>
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
                        <td class="px-6 py-3 text-right text-gray-600">
                            @if($b->fine_amount > 0)
                                <span class="text-red-600 font-medium">₱{{ number_format($b->fine_amount, 2) }}</span>
                            @else
                                —
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
