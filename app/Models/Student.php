<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\QueryOptimizer;

class Student extends Model
{
    use QueryOptimizer;
    protected $fillable = [
        'name',
        'email',
        'student_id',
        'phone',
        'address',
        'profile_image',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profile_image_url'];

    /**
     * Get the student's profile image URL.
     */
    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image) {
            // Check if it's already a full URL or a base64 string
            if (preg_match('/^(http|https|data:)/', $this->profile_image)) {
                return $this->profile_image;
            }
            
            // Strip any leading "images/" or "/images/" to prevent double prefixing
            $path = ltrim($this->profile_image, '/');
            if (str_starts_with($path, 'images/')) {
                $path = substr($path, 7);
            }
            
            // Otherwise, it's a filename in public/images/
            return asset('images/' . $path);
        }

        // Return empty string or handle default in the view if preferred
        return '';
    }

    public function borrowTransactions(): HasMany
    {
        return $this->hasMany(BorrowTransaction::class);
    }

    public function getTotalFinesAttribute()
    {
        return $this->borrowTransactions->sum(fn($bt) => $bt->total_fine);
    }
}
