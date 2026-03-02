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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['cover_image_url'];

    /**
     * Get the book's cover image URL.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            // Check if it's already a full URL or a base64 string
            if (preg_match('/^(http|https|data:)/', $this->cover_image)) {
                return $this->cover_image;
            }
            
            // Strip any leading "images/" or "/images/"
            $path = ltrim($this->cover_image, '/');
            if (str_starts_with($path, 'images/')) {
                $path = substr($path, 7);
            }
            
            return asset('images/' . $path);
        }

        return asset('images/default-book-cover.png');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function borrowTransactions(): HasMany
    {
        return $this->hasMany(BorrowTransaction::class);
    }
}
