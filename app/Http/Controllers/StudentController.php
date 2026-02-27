<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\BorrowTransaction;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'newest'); // Default to newest first

        $query = Student::with('borrowTransactions');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($filter === 'active') {
            $query->whereHas('borrowTransactions', function($q) {
                $q->whereIn('status', ['borrowed', 'partially_returned']);
            });
        }

        // Apply sorting - newest first by default
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Filter for newly added records (last 7 days)
        if ($filter === 'new') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        $students = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('students.partials.table', compact('students'))->render();
        }

        return view('students.index', compact('students', 'search', 'filter', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:students',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|string',
        ]);

        $student = Student::create($validated);

        return redirect()->route('students.show', $student)->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student, Request $request)
    {
        $student->load('borrowTransactions.book');
        $borrowTransactions = $student->borrowTransactions()->paginate(10);
        
        if ($request->ajax()) {
            return view('students.partials.details', compact('student', 'borrowTransactions'))->render();
        }

        return view('students.show', compact('student', 'borrowTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:students,email,' . $student->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
