<?php

namespace App\Http\Controllers;

use App\Models\BorrowTransaction;
use App\Models\Student;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = BorrowTransaction::with('student', 'book');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('book', function($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('borrow-transactions.partials.table', compact('transactions'))->render();
        }

        return view('borrow-transactions.index', compact('transactions', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::all();
        $books = Book::where('available_quantity', '>', 0)->get();
        return view('borrow-transactions.create', compact('students', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'pin' => 'required|string',
            'quantity_borrowed' => 'required|integer|min:1',
            'due_date' => 'required|date|after:today',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($student->pin !== $validated['pin']) {
            return redirect()->back()
                ->withErrors(['pin' => 'Security PIN mismatch. Please check your PIN and try again.'])
                ->withInput();
        }

        $book = Book::find($validated['book_id']);

        if ($book->available_quantity < $validated['quantity_borrowed']) {
            return redirect()->back()
                ->withErrors(['quantity_borrowed' => 'Not enough books available.'])
                ->withInput();
        }

        $transaction = BorrowTransaction::create([
            'student_id' => $validated['student_id'],
            'book_id' => $validated['book_id'],
            'borrow_date' => now(),
            'due_date' => $validated['due_date'],
            'quantity_borrowed' => $validated['quantity_borrowed'],
            'quantity_returned' => 0,
            'status' => 'borrowed',
            'fine_amount' => 0,
        ]);

        // Update book availability
        $book->decrement('available_quantity', $validated['quantity_borrowed']);

        return redirect()->route('borrow-transactions.show', $transaction)->with('success', 'Book borrowed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BorrowTransaction $borrowTransaction)
    {
        $borrowTransaction->load('student', 'book');
        return view('borrow-transactions.show', compact('borrowTransaction'));
    }

    /**
     * Show the form for returning books.
     */
    public function edit(BorrowTransaction $borrowTransaction)
    {
        return view('borrow-transactions.return', compact('borrowTransaction'));
    }

    /**
     * Process book return.
     */
    public function update(Request $request, BorrowTransaction $borrowTransaction)
    {
        if ($borrowTransaction->status === 'returned') {
            return redirect()->back()->withErrors(['error' => 'Books already returned.']);
        }

        $validated = $request->validate([
            'quantity_returned' => 'required|integer|min:1|max:' . ($borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned),
        ]);

        $returningQuantity = $validated['quantity_returned'];
        $borrowTransaction->quantity_returned += $returningQuantity;

        // Update status
        if ($borrowTransaction->quantity_returned >= $borrowTransaction->quantity_borrowed) {
            $borrowTransaction->status = 'returned';
            $borrowTransaction->return_date = now();
        } else {
            $borrowTransaction->status = 'partially_returned';
        }

        // Calculate fine
        $borrowTransaction->updateFine();
        $borrowTransaction->save();

        // Update book availability
        $borrowTransaction->book->increment('available_quantity', $returningQuantity);

        return redirect()->route('borrow-transactions.show', $borrowTransaction)->with('success', 'Books returned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BorrowTransaction $borrowTransaction)
    {
        if ($borrowTransaction->status !== 'returned') {
            return redirect()->back()->withErrors(['error' => 'Cannot delete active transaction.']);
        }

        $borrowTransaction->delete();
        return redirect()->route('borrow-transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Get overdue books.
     */
    public function overdue()
    {
        $overdueTransactions = BorrowTransaction::whereIn('status', ['borrowed', 'partially_returned'])
            ->where('due_date', '<', now())
            ->with('student', 'book')
            ->paginate(15);

        return view('borrow-transactions.overdue', compact('overdueTransactions'));
    }

    /**
     * Get student's borrow history.
     */
    public function studentHistory(Student $student)
    {
        $transactions = $student->borrowTransactions()->with('book')->paginate(15);
        return view('borrow-transactions.student-history', compact('student', 'transactions'));
    }
}
