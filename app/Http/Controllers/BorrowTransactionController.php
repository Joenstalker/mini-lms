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

        // We want to group by student to show one row per student who has/had transactions
        $query = BorrowTransaction::select('student_id')
            ->selectRaw('count(*) as total_count')
            ->selectRaw('sum(case when status != \'returned\' then 1 else 0 end) as active_count')
            ->selectRaw('sum(fine_amount) as total_fines')
            ->selectRaw('max(created_at) as last_transaction_at')
            ->with(['student'])
            ->groupBy('student_id');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($filter === 'overdue') {
            $query->whereHas('student.borrowTransactions', function($q) {
                $q->whereIn('status', ['borrowed', 'partially_returned'])
                  ->where('due_date', '<', now());
            });
        } elseif ($filter === 'borrowed') {
            $query->having('active_count', '>', 0);
        } elseif ($filter === 'returned') {
            // Show students who have at least one book fully returned
            $query->whereHas('student.borrowTransactions', function($q) {
                $q->where('status', 'returned');
            });
        }

        match ($sort) {
            'oldest' => $query->orderBy('last_transaction_at', 'asc'),
            default  => $query->orderBy('last_transaction_at', 'desc'),
        };

        $perPage      = $request->input('per_page', 15);
        $groupedTransactions = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('borrow-transactions.partials.table', compact('groupedTransactions'))->render();
        }

        return view('borrow-transactions.index', compact('groupedTransactions', 'search', 'sort', 'filter'));
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
     * Allows multiple copies of the same book title in one transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'borrow_date' => 'nullable|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            // Support both books array (bulk) OR single book_id/quantity
            'books' => 'nullable|array',
            'books.*.id' => 'required_with:books|exists:books,id',
            'books.*.quantity' => 'required_with:books|integer|min:1',
            'book_id' => 'required_without:books|exists:books,id',
            'quantity_borrowed' => 'required_without:books|integer|min:1',
        ]);

        try {
            \DB::beginTransaction();

            $borrowDate = $validated['borrow_date'] ? Carbon::parse($validated['borrow_date']) : now();
            $dueDate = Carbon::parse($validated['due_date'])->endOfDay();

            // Normalize to a list of books to process
            $booksToProcess = [];
            if (!empty($validated['books'])) {
                foreach ($validated['books'] as $b) {
                    $booksToProcess[] = ['id' => $b['id'], 'qty' => (int)$b['quantity']];
                }
            } else {
                $booksToProcess[] = ['id' => $validated['book_id'], 'qty' => (int)$validated['quantity_borrowed']];
            }

            foreach ($booksToProcess as $item) {
                $book = Book::findOrFail($item['id']);
                $qty = $item['qty'];

                if ($book->available_quantity < $qty) {
                    throw new \Exception("Insufficient stock for '{$book->title}'. Only {$book->available_quantity} available.");
                }

                BorrowTransaction::create([
                    'student_id' => $validated['student_id'],
                    'book_id' => $book->id,
                    'borrow_date' => $borrowDate,
                    'due_date' => $dueDate,
                    'quantity_borrowed' => $qty,
                    'quantity_returned' => 0,
                    'status' => 'borrowed',
                    'fine_amount' => 0,
                ]);

                $book->decrement('available_quantity', $qty);
            }

            \DB::commit();
            CacheService::invalidateDashboardCache();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lending processed successfully for all selected books.'
                ]);
            }

            return redirect()->route('borrow-transactions.index')->with('success', 'Lending processed successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display transaction details.
     */
    public function show(BorrowTransaction $borrowTransaction, Request $request)
    {
        $student = $borrowTransaction->student;
        $student->load(['borrowTransactions.book']);
        
        $borrowTransactions = $student->borrowTransactions()->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
            return view('borrow-transactions.partials.details', compact('student', 'borrowTransactions'))->render();
        }

        return view('borrow-transactions.show', compact('student', 'borrowTransactions'));
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

        // Calculate fine for the quantity being returned late
        $fineForReturningQuantity = 0;
        if (now()->greaterThan($borrowTransaction->due_date)) {
            $overdueDays = (int) $borrowTransaction->due_date->diffInDays(now()->startOfDay()) + 1;
            $fineForReturningQuantity = $overdueDays * 10 * $returningQuantity;
        }

        $borrowTransaction->quantity_returned += $returningQuantity;
        $borrowTransaction->fine_amount += $fineForReturningQuantity;
        
        // Determine new status
        if ($borrowTransaction->quantity_returned >= $borrowTransaction->quantity_borrowed) {
            $borrowTransaction->status      = 'returned';
            $borrowTransaction->return_date = now();
        } else {
            $borrowTransaction->status = 'partially_returned';
        }
        
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'transaction' => $borrowTransaction->load(['student', 'book'])
            ]);
        }

        return redirect()
            ->route('borrow-transactions.show', $borrowTransaction)
            ->with('success', $message);
    }

    /**
     * Delete a completed transaction.
     */
    public function destroy(BorrowTransaction $borrowTransaction, Request $request)
    {
        // Restore inventory if the transaction is still active
        if ($borrowTransaction->status !== 'returned') {
            $unreturnedQuantity = $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned;
            if ($unreturnedQuantity > 0) {
                $borrowTransaction->book->increment('available_quantity', $unreturnedQuantity);
            }
        }

        $borrowTransaction->delete();

        CacheService::invalidateDashboardCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully.'
            ]);
        }

        return redirect()
            ->route('borrow-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Delete all transactions for a specific student.
     */
    public function destroyGroup(Student $student, Request $request)
    {
        $transactions = $student->borrowTransactions;

        foreach ($transactions as $transaction) {
            // Restore inventory if the transaction is still active
            if ($transaction->status !== 'returned') {
                $unreturnedQuantity = $transaction->quantity_borrowed - $transaction->quantity_returned;
                if ($unreturnedQuantity > 0) {
                    $transaction->book->increment('available_quantity', $unreturnedQuantity);
                }
            }
            $transaction->delete();
        }

        CacheService::invalidateDashboardCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All transaction history for this student has been cleared.'
            ]);
        }

        return redirect()
            ->route('borrow-transactions.index')
            ->with('success', 'Transaction history cleared successfully.');
    }

    /**
     * Compute fine: ₱10 × overdue days × remaining quantity.
     * Deprecated: Use model's calculateFine() instead.
     */
    private function computeFine(BorrowTransaction $transaction): float
    {
        return $transaction->calculateFine();
    }
}
