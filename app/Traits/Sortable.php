<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Scope a query to sort results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, Request $request, $sortBy)
    {
        $sortables = data_get($this, 'sortables', []);

        // Lấy column để Sort
        $sort = $request->get('sort');

        // Lấy cách sort ('desc','asc',)
        $direction = $request->get('direction', $sortBy);

        // Đảm bảo cột sắp xếp là một column sort ở model có thể sắp xếp của mô hình
        // và giá trị là ('asc' or 'desc')
        if ($sort
            && in_array($sort, $sortables)
            && $direction
            && in_array($direction, ['asc', 'desc'])) {
            // Trả về kết quả sort
            return $query->orderBy($sort, $direction);
        }

        // Nếu ko có direction hoặc ghi bậy thì sẽ trả về kết quả mặc định
        return $query;
    }
}