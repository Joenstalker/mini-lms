<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('student_number', 'like', '%' . $request->search . '%');
        }
        $students = $query->latest()->paginate(10)->withQueryString();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'student_number' => 'required|string|unique:students',
            'email'          => 'nullable|email|unique:students',
            'phone'          => 'nullable|string|max:20',
        ]);

        Student::create($request->only('name', 'student_number', 'email', 'phone'));
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        $student->load('borrowings.book');
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'student_number' => 'required|string|unique:students,student_number,' . $student->id,
            'email'          => 'nullable|email|unique:students,email,' . $student->id,
            'phone'          => 'nullable|string|max:20',
        ]);

        $student->update($request->only('name', 'student_number', 'email', 'phone'));
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
