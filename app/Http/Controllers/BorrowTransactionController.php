<?php

namespace App\Http\Controllers;

use App\Models\BorrowTransaction;
use App\Models\Student;
use App\Models\Book;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowTransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort   = $request->input('sort', 'newest');
        $filter = $request->input('filter');

        $query = BorrowTransaction::with(['student', 'book']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('book', fn($bq) => $bq->where('title', 'like', "%{$search}%"));
            });
        }

        match ($filter) {
            'borrowed'           => $query->where('status', 'borrowed'),
            'returned'           => $query->where('status', 'returned'),
            'partially_returned' => $query->where('status', 'partially_returned'),
            'overdue'            => $query->whereIn('status', ['borrowed', 'partially_returned'])
                                          ->where('due_date', '<', now()->startOfDay()),
            default              => null,
        };

        match ($sort) {
            'oldest'   => $query->orderBy('created_at', 'asc'),
            'due_asc'  => $query->orderBy('due_date', 'asc'),
            'due_desc' => $query->orderBy('due_date', 'desc'),
            default    => $query->orderBy('created_at', 'desc'),
        };

        $perPage      = $request->input('per_page', 15);
        $transactions = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('borrow-transactions.partials.table', compact('transactions'))->render();
        }

        return view('borrow-transactions.index', compact('transactions', 'search', 'sort', 'filter'));
    }

    /**
     * Show the form for creating a new borrow transaction (admin only).
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $books    = Book::where('available_quantity', '>', 0)->orderBy('title')->get();

        return view('borrow-transactions.create', compact('students', 'books'));
    }

    /**
     * Store a new borrow transaction (admin processes at the counter).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:students,id',
            'book_id'           => 'required|exists:books,id',
            'quantity_borrowed' => 'required|integer|min:1',
            'due_date'          => 'required|date|after:today',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->available_quantity < $validated['quantity_borrowed']) {
            return redirect()->back()
                ->withErrors(['quantity_borrowed' => "Only {$book->available_quantity} cop(ies) available."])
                ->withInput();
        }

        $transaction = BorrowTransaction::create([
            'student_id'        => $validated['student_id'],
            'book_id'           => $validated['book_id'],
            'borrow_date'       => now(),
            'due_date'          => $validated['due_date'],
            'quantity_borrowed' => $validated['quantity_borrowed'],
            'quantity_returned' => 0,
            'status'            => 'borrowed',
            'fine_amount'       => 0,
        ]);

        $book->decrement('available_quantity', $validated['quantity_borrowed']);

        CacheService::invalidateDashboardCache();

        return redirect()
            ->route('borrow-transactions.show', $transaction)
            ->with('success', "Book borrowed successfully. Due: {$transaction->due_date->format('M d, Y')}");
    }

    /**
     * Display transaction details.
     */
    public function show(BorrowTransaction $borrowTransaction, Request $request)
    {
        $borrowTransaction->load(['student', 'book']);

        if ($request->ajax()) {
            return view('borrow-transactions.partials.details', compact('borrowTransaction'))->render();
        }

        return view('borrow-transactions.show', compact('borrowTransaction'));
    }

    /**
     * Show the return form for a transaction.
     */
    public function edit(BorrowTransaction $borrowTransaction, Request $request)
    {
        $borrowTransaction->load(['student', 'book']);

        if ($borrowTransaction->status === 'returned') {
            if ($request->ajax()) {
                return '<div class="alert alert-info">Already returned.</div>';
            }
            return redirect()
                ->route('borrow-transactions.show', $borrowTransaction)
                ->with('info', 'All books for this transaction have already been returned.');
        }

        if ($request->ajax()) {
            return view('borrow-transactions.partials.return_form', compact('borrowTransaction'))->render();
        }

        return view('borrow-transactions.return', compact('borrowTransaction'));
    }

    /**
     * Process book return (admin confirms return at the counter).
     * Supports partial returns.
     */
    public function update(Request $request, BorrowTransaction $borrowTransaction)
    {
        if ($borrowTransaction->status === 'returned') {
            return redirect()->back()->withErrors(['error' => 'All books already returned.']);
        }

        $maxReturnable = $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned;

        $validated = $request->validate([
            'quantity_returned' => "required|integer|min:1|max:{$maxReturnable}",
        ]);

        $returningQuantity = $validated['quantity_returned'];

        // Load the book to update inventory
        $book = $borrowTransaction->book;

        $borrowTransaction->quantity_returned += $returningQuantity;

        // Determine new status
        if ($borrowTransaction->quantity_returned >= $borrowTransaction->quantity_borrowed) {
            $borrowTransaction->status      = 'returned';
            $borrowTransaction->return_date = now();
        } else {
            $borrowTransaction->status = 'partially_returned';
        }

        // Calculate fine based on overdue days (₱10/day × books still unreturned before this return)
        $borrowTransaction->fine_amount = $this->computeFine($borrowTransaction);
        $borrowTransaction->save();

        // Restore inventory
        $book->increment('available_quantity', $returningQuantity);

        CacheService::invalidateDashboardCache();

        $message = $borrowTransaction->status === 'returned'
            ? 'All books returned successfully.'
            : "{$returningQuantity} book(s) returned. " . ($maxReturnable - $returningQuantity) . " still out.";

        if ($borrowTransaction->fine_amount > 0) {
            $message .= " Fine due: ₱{$borrowTransaction->fine_amount}";
        }

        return redirect()
            ->route('borrow-transactions.show', $borrowTransaction)
            ->with('success', $message);
    }

    /**
     * Delete a completed transaction.
     */
    public function destroy(BorrowTransaction $borrowTransaction)
    {
        if ($borrowTransaction->status !== 'returned') {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete an active transaction. Return all books first.']);
        }

        $borrowTransaction->delete();

        CacheService::invalidateDashboardCache();

        return redirect()
            ->route('borrow-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Compute fine: ₱10 × overdue days × remaining quantity.
     */
    private function computeFine(BorrowTransaction $transaction): float
    {
        // Overdue status starts the day AFTER the due date.
        if (Carbon::now()->startOfDay()->lessThanOrEqualTo($transaction->due_date->startOfDay())) {
            return 0;
        }

        $overdueDays       = (int) Carbon::now()->startOfDay()->diffInDays($transaction->due_date->startOfDay());
        $remainingQuantity = $transaction->quantity_borrowed - $transaction->quantity_returned;

        return $overdueDays * 10 * max($remainingQuantity, 0);
    }
}
