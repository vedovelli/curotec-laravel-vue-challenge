<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Transforms Laravel pagination objects into consistent frontend-friendly structures.
 * 
 * This trait provides reusable methods for converting Laravel's pagination
 * objects into standardized formats that match frontend interface expectations.
 */
trait TransformsPagination
{
    /**
     * Transform Laravel pagination to match frontend interface.
     * 
     * Creates a standardized pagination structure with data, meta, and links
     * that can be consumed by frontend components consistently.
     *
     * @param LengthAwarePaginator<int, mixed> $paginator The Laravel paginator instance
     * @return array{data: array<mixed>, meta: array<string, int|null>, links: array<string, mixed>} The transformed pagination structure
     */
    protected function transformPagination(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => $this->transformPaginationMeta($paginator),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    /**
     * Transform pagination metadata.
     * 
     * Extracts and formats the pagination metadata in a consistent structure.
     *
     * @param LengthAwarePaginator<int, mixed> $paginator The Laravel paginator instance
     * @return array<string, int|null> The pagination metadata
     */
    protected function transformPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * Transform pagination with additional metadata for dashboard-style views.
     * 
     * Includes extra metadata like has_more_pages that might be useful
     * for dashboard or overview components.
     *
     * @param LengthAwarePaginator<int, mixed> $paginator The Laravel paginator instance
     * @return array<string, int|bool|array<string, mixed>|null> The enhanced pagination structure
     */
    protected function transformPaginationWithExtras(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }
} 