<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'student_id', 'book_id', 'borrowed_at', 'due_date',
        'returned_at', 'fine_amount', 'status',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_date'    => 'date',
        'returned_at' => 'date',
    ];

    const FINE_PER_DAY = 5.00;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function computeFine(): float
    {
        $referenceDate = $this->returned_at ?? Carbon::today();
        if ($referenceDate->gt($this->due_date)) {
            $daysOverdue = $this->due_date->diffInDays($referenceDate);
            return $daysOverdue * self::FINE_PER_DAY;
        }
        return 0.0;
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'returned' && Carbon::today()->gt($this->due_date);
    }
}
