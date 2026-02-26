<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for optimizing Eloquent queries
 */
trait QueryOptimizer
{
    /**
     * Apply eager loading to avoid N+1 queries
     */
    public function scopeWithRelations(Builder $query, array $relations = []): Builder
    {
        return $query->with($relations);
    }

    /**
     * Scope for efficient pagination
     */
    public function scopeCursorPaginate(Builder $query, int $perPage = 15, ?string $cursor = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $query->orderBy('id')->paginate($perPage);
    }

    /**
     * Scope for cursor-based pagination (more efficient for large datasets)
     */
    public function scopeCursorPaginateOptimized(Builder $query, int $perPage = 15, ?string $cursor = null): \Illuminate\Pagination\CursorPaginator
    {
        return $query->orderBy('id')->cursorPaginate($perPage);
    }

    /**
     * Scope to select only needed columns
     */
    public function scopeSelectMinimal(Builder $query): Builder
    {
        return $query->select(['id', 'created_at', 'updated_at']);
    }

    /**
     * Scope to add database indexes hint
     */
    public function scopeOptimizedIndex(Builder $query): Builder
    {
        // This is a placeholder - actual indexes should be added via migrations
        return $query;
    }

    /**
     * Scope for search functionality with index awareness
     */
    public function scopeSearch(Builder $query, ?string $search, array $columns = []): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }
        });
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatusFilter(Builder $query, ?string $status): Builder
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope for date range filtering
     */
    public function scopeDateRange(Builder $query, ?string $startDate, ?string $endDate, string $column = 'created_at'): Builder
    {
        if ($startDate) {
            $query->where($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->where($column, '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope for ordering
     */
    public function scopeOrdered(Builder $query, string $column = 'id', string $direction = 'asc'): Builder
    {
        return $query->orderBy($column, $direction);
    }

    /**
     * Get counts with caching
     */
    public static function getCachedCount(int $cacheMinutes = 5): int
    {
        $cacheKey = self::class . '_count_' . date('YmdH');
        
        return cache()->remember($cacheKey, $cacheMinutes * 60, function () {
            return self::count();
        });
    }

    /**
     * Clear count cache
     */
    public static function clearCountCache(): void
    {
        // Clear hourly caches
        for ($i = 0; $i < 24; $i++) {
            cache()->forget(self::class . '_count_' . date('YmdH', strtotime("-{$i} hours")));
        }
    }
}
