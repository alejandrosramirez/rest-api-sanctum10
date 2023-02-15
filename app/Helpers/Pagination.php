<?php

namespace App\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Pagination
{
    /**
     * Paginate a collection/array.
     *
     * @param  \Illuminate\Support\Collection|array  $items
     * @param  int  $perPage
     * @param  string  $pageName
     * @param  ?int  $page
     */
    public static function paginate(Collection|array $items, int $perPage, string $pageName = 'page', int $page = null): LengthAwarePaginator
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = $items->count();

        return self::paginator($items->forPage($page, $perPage)->values(), $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }
}
