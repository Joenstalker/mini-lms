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
                $q->whereHas('student', fn($sq) => 
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('student_id', 'like', "%{$search}%")
                )
                ->orWhereHas('book', fn($bq) => 
                    $bq->where('title', 'like', "%{$search}%")
                       ->orWhereHas('authors', fn($aq) => $aq->where('name', 'like', "%{$search}%"))
                )
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        match ($filter) {
            'borrowed'           => $query->where('status', 'borrowed'),
            'returned'           => $query->where('status', 'returned'),
            'partially_returned' => $query->where('status', 'partially_returned'),
            'overdue'            => $query->whereIn('status', ['borrowed', 'partially_returned'])
                                          ->where('due_date', '<', now()),
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
     * Allows multiple copies of the same book title in one transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'books' => 'required|array|min:1',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            \DB::beginTransaction();

            foreach ($validated['books'] as $bookData) {
                $book = Book::findOrFail($bookData['id']);
                $qty = (int)$bookData['quantity'];

                if ($book->available_quantity < $qty) {
                    throw new \Exception("Insufficient stock for '{$book->title}'. Only {$book->available_quantity} available.");
                }

                BorrowTransaction::create([
                    'student_id' => $validated['student_id'],
                    'book_id' => $book['id'],
                    'borrow_date' => Carbon::parse($validated['borrow_date']),
                    'due_date' => Carbon::parse($validated['due_date'])->endOfDay(),
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
    public function destroy(BorrowTransaction $borrowTransaction)
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

        return redirect()
            ->route('borrow-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
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
