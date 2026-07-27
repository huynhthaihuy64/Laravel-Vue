<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    /**
     * Scope a query to sort results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, $data)
    {
        if (isset($data['field']) && isset($data['sortType'])) {
            return $query->orderBy($data['field'], $data['sortType']);
        } else {
            return $query->orderBy('created_at', 'DESC');
        }
    }
}