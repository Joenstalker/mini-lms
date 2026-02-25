<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BorrowTransaction extends Model
{
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
        if ($this->status === 'returned' || Carbon::now()->lessThanOrEqualTo($this->due_date)) {
            return 0;
        }

        $overdueDays = Carbon::now()->diffInDays($this->due_date);
        $remainingQuantity = $this->quantity_borrowed - $this->quantity_returned;
        
        return $overdueDays * 10 * $remainingQuantity;
    }

    public function updateFine()
    {
        $this->fine_amount = $this->calculateFine();
        $this->save();
    }
}
