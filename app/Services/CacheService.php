<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Cache duration in seconds (5 minutes default)
     */
    const CACHE_DURATION = 300;

    /**
     * Get cached dashboard statistics
     */
    public static function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', self::CACHE_DURATION, function () {
            return [
                'total_books' => DB::table('books')->count(),
                'total_students' => DB::table('students')->count(),
                'active_borrows' => DB::table('borrow_transactions')
                    ->whereIn('status', ['borrowed', 'partially_returned'])
                    ->count(),
                'overdue_items' => DB::table('borrow_transactions')
                    ->whereIn('status', ['borrowed', 'partially_returned'])
                    ->where('due_date', '<', now())
                    ->count(),
            ];
        });
    }

    /**
     * Get cached book count
     */
    public static function getBookCount(): int
    {
        return Cache::remember('book_count', self::CACHE_DURATION, function () {
            return DB::table('books')->count();
        });
    }

    /**
     * Get cached student count
     */
    public static function getStudentCount(): int
    {
        return Cache::remember('student_count', self::CACHE_DURATION, function () {
            return DB::table('students')->count();
        });
    }

    /**
     * Get cached active borrows count
     */
    public static function getActiveBorrowsCount(): int
    {
        return Cache::remember('active_borrows_count', self::CACHE_DURATION, function () {
            return DB::table('borrow_transactions')
                ->whereIn('status', ['borrowed', 'partially_returned'])
                ->count();
        });
    }

    /**
     * Get cached overdue count
     */
    public static function getOverdueCount(): int
    {
        return Cache::remember('overdue_count', self::CACHE_DURATION, function () {
            return DB::table('borrow_transactions')
                ->whereIn('status', ['borrowed', 'partially_returned'])
                ->where('due_date', '<', now())
                ->count();
        });
    }

    /**
     * Invalidate dashboard cache
     */
    public static function invalidateDashboardCache(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('book_count');
        Cache::forget('student_count');
        Cache::forget('active_borrows_count');
        Cache::forget('overdue_count');
    }

    /**
     * Get cached books by IDs with eager loading
     */
    public static function getCachedBooks(array $ids)
    {
        $cacheKey = 'books_' . md5(serialize($ids));
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($ids) {
            return \App\Models\Book::with('authors')
                ->whereIn('id', $ids)
                ->get();
        });
    }

    /**
     * Get cached authors list
     */
    public static function getCachedAuthors()
    {
        return Cache::remember('authors_list', self::CACHE_DURATION, function () {
            return \App\Models\Author::all();
        });
    }

    /**
     * Invalidate authors cache
     */
    public static function invalidateAuthorsCache(): void
    {
        Cache::forget('authors_list');
    }

    /**
     * Get cached students list
     */
    public static function getCachedStudents()
    {
        return Cache::remember('students_list', self::CACHE_DURATION, function () {
            return \App\Models\Student::all();
        });
    }

    /**
     * Invalidate students cache
     */
    public static function invalidateStudentsCache(): void
    {
        Cache::forget('students_list');
    }
}
