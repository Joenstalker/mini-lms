<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Traits\QueryOptimizer;

class BorrowTransaction extends Model
{
    use QueryOptimizer;
    protected $fillable = [
        'student_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'fine_amount',
        'quantity_borrowed',
        'quantity_returned',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function calculateFine()
    {
        // Total fine = locked fine (from previous late returns) + live fine for currently unreturned items.
        
        // If not yet overdue, total fine is just whatever was locked from previous partial returns.
        if (now()->lessThanOrEqualTo($this->due_date)) {
            return (float) $this->fine_amount;
        }

        // If overdue, calculate live fine for remaining books.
        // Overdue status starts exactly at the deadline (11:59:59 PM).
        // Any time on the next day counts as 1 day overdue.
        // Formula: diffInDays(now->startOfDay, due_date) + 1
        $overdueDays = (int) $this->due_date->diffInDays(now()->startOfDay()) + 1;
        
        $remainingQuantity = $this->quantity_borrowed - $this->quantity_returned;
        
        // Live fine for remaining books
        $liveFine = $overdueDays * 10 * max($remainingQuantity, 0);
        
        return (float) $this->fine_amount + $liveFine;
    }

    /**
     * Get the current total fine including both locked and live fines.
     */
    public function getTotalFineAttribute()
    {
        return $this->calculateFine();
    }

    /**
     * Check if the transaction is currently overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'returned' && now()->greaterThan($this->due_date);
    }

    public function updateFine()
    {
        // Typically fine_amount is updated during the return process.
        // This helper can be used to manually synchronize the locked fine if needed.
        $this->save();
    }
}
