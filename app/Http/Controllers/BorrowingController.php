<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        // Refresh overdue statuses
        Borrowing::where('status', 'borrowed')
            ->whereDate('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $query = Borrowing::with(['student', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('book', fn($q) => $q->where('title', 'like', '%' . $request->search . '%'));
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();
        return view('borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $books    = Book::where('available_quantity', '>', 0)->orderBy('title')->get();
        return view('borrowings.create', compact('students', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'book_id'     => 'required|exists:books,id',
            'borrowed_at' => 'required|date',
            'due_date'    => 'required|date|after_or_equal:borrowed_at',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_quantity < 1) {
            return back()->withErrors(['book_id' => 'This book is not available for borrowing.'])->withInput();
        }

        Borrowing::create([
            'student_id'  => $request->student_id,
            'book_id'     => $request->book_id,
            'borrowed_at' => $request->borrowed_at,
            'due_date'    => $request->due_date,
            'status'      => 'borrowed',
            'fine_amount' => 0,
        ]);

        $book->decrement('available_quantity');

        return redirect()->route('borrowings.index')->with('success', 'Book borrowed successfully.');
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['student', 'book']);
        $fine = $borrowing->computeFine();
        return view('borrowings.show', compact('borrowing', 'fine'));
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return back()->with('error', 'This book has already been returned.');
        }

        $returnDate  = Carbon::today();
        $fine        = $borrowing->computeFine();

        $borrowing->update([
            'returned_at' => $returnDate,
            'fine_amount' => $fine,
            'status'      => 'returned',
        ]);

        $borrowing->book->increment('available_quantity');

        return redirect()->route('borrowings.index')
            ->with('success', 'Book returned successfully.' . ($fine > 0 ? " Fine: ₱{$fine}" : ''));
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'returned') {
            $borrowing->book->increment('available_quantity');
        }
        $borrowing->delete();
        return redirect()->route('borrowings.index')->with('success', 'Borrowing record deleted.');
    }
}
