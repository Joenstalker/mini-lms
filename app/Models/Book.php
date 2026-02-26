<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\QueryOptimizer;

class Book extends Model
{
    use QueryOptimizer;
    protected $fillable = [
        'title',
        'description',
        'total_quantity',
        'available_quantity',
        'publisher',
        'published_year',
        'cover_image',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function borrowTransactions(): HasMany
    {
        return $this->hasMany(BorrowTransaction::class);
    }
}
