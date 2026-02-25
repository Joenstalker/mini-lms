<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'pin',
    ];

    public function borrowTransactions(): HasMany
    {
        return $this->hasMany(BorrowTransaction::class);
    }

    public function getOutstandingFineAttribute()
    {
        return $this->borrowTransactions()
            ->whereIn('status', ['borrowed', 'partially_returned'])
            ->sum('fine_amount');
    }
}
