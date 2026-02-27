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
        // A book is only overdue starting the day AFTER the due date.
        // If today is the due date, it is NOT overdue.
        if ($this->status === 'returned' || Carbon::now()->startOfDay()->lessThanOrEqualTo($this->due_date->startOfDay())) {
            return 0;
        }

        // Overdue days = days passed since the due date (excluding the due date itself).
        $overdueDays = Carbon::now()->startOfDay()->diffInDays($this->due_date->startOfDay());
        $remainingQuantity = $this->quantity_borrowed - $this->quantity_returned;
        
        return $overdueDays * 10 * max($remainingQuantity, 0);
    }

    public function updateFine()
    {
        $this->fine_amount = $this->calculateFine();
        $this->save();
    }
}
